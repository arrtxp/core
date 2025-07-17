<?php

namespace Arrtxp\Core;

use Arrtxp\Core\Filters\ToArray;
use Arrtxp\Core\Filters\ToBool;
use Arrtxp\Core\Filters\ToFloat;
use Arrtxp\Core\Filters\ToInt;
use Arrtxp\Core\Filters\ToNull;
use Arrtxp\Core\Filters\ToString;
use Arrtxp\Core\Validators\ArrayLength;
use Arrtxp\Core\Validators\Between;
use Arrtxp\Core\Validators\Enum;
use Arrtxp\Core\Validators\StringLength;
use DateTime;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;

abstract class Handler implements MiddlewareInterface, ServiceConstants
{
    protected InputFilters $inputFilters;

    abstract protected function run(ServerRequestInterface $request): ServerRequestInterface;

    protected function runBefore(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request;
    }

    private function buildParams(ServerRequestInterface $request): ServerRequestInterface
    {
        $params = array_merge_recursive(
            $request->getQueryParams(),
            $request->getParsedBody(),
            $request->getAttribute(RouteResult::class)->getMatchedParams() ?? [],
        );

        if ($request->getMethod() !== ServiceConstants::METHOD_GET) {
            $params = array_merge(
                $params,
                json_decode(file_get_contents('php://input'), true) ?? []
            );
        }

        foreach ($_FILES ?? [] as $name => $file) {
            if (empty($file['error'])) {
                $params[$name] = $file;
            }
        }

        return $request->withAttribute(self::PARAMS, $params);
    }

    public function __invoke(ServerRequestInterface $request): ServerRequestInterface
    {
        $startHrtime = hrtime(true);

        $params = $request->getAttribute(self::PARAMS, []);
        [$inputs, $types] = $this->buildInputs(null, $request->getMethod());
        $this->inputFilters->filterParams($inputs, $params);

        $request = $request->withAttribute(self::PARAMS, $params);
        $request = $this->runBefore($request);

        $errors = [];
        $this->inputFilters->validation($inputs, $params, $errors);

        $request = $request->withAttribute(self::ERRORS, $errors);

        if (isset($types)) {
            $this->setParams($this, $params, $types);
        }

        if ($errors) {
            return $this->runWhenError(
                $request->withAttribute(self::ERRORS, $errors)
                    ->withAttribute(self::HTTP_CODE, self::HTTP_CODE_400)
                    ->withAttribute(self::EXECUTE_TIME, (hrtime(true) - $startHrtime) / 1e+6)
            );
        }

        $request = $this->run($request);

        return $request->withAttribute(self::EXECUTE_TIME, (hrtime(true) - $startHrtime) / 1e+6);
    }

    protected function returnError(
        ServerRequestInterface $request,
        string $field,
        string $message
    ): ServerRequestInterface {
        $errors = $request->getAttribute(self::ERRORS, []);
        $errors["/$field"] = $message;

        return $this->runWhenError(
            $request->withAttribute(self::ERRORS, $errors)
                ->withAttribute(self::HTTP_CODE, self::HTTP_CODE_400)
        );
    }

    private function setParams(object $obj, array $params, array $types): void
    {
        foreach ($params as $key => $value) {
            if (!property_exists($obj, $key)) {
                continue;
            }

            if (in_array($types[$key], ['int', 'float', 'bool', 'string', 'array', 'null'])) {
                $obj->$key = $value;
            } elseif (enum_exists($types[$key]['name'])) {
                $obj->$key = $value ? $types[$key]['name']::from($value) : $value;
            } elseif (!empty($types[$key]['collection'])) {
                $collection = [];
                foreach ($params[$key] as $_params) {
                    $col = new $types[$key]['name']();
                    $this->setParams($col, $_params, $types[$key]['inputs']);

                    $collection[] = $col;
                }

                $obj->$key = $collection;
            } elseif ($types[$key]['name'] === DateTime::class) {
                $obj->$key = $params[$key] ? new $types[$key]['name']($params[$key]) : null;
            } else {
                $obj->$key = new $types[$key]['name']();
                if ($params[$key] === null) {
                    $obj->$key = null;
                } else {
                    $this->setParams($obj->$key, $params[$key], $types[$key]['inputs']);
                }
            }
        }
    }

    protected function runWhenError(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withAttribute(self::HTTP_CODE, 400);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getAttribute(self::ERRORS)) {
            return $handler->handle($request);
        }

        $request = $this->buildParams($request);

        return $handler->handle($this($request));
    }

    public function setInputFilters(InputFilters $inputFilters): void
    {
        $this->inputFilters = $inputFilters;
    }

    private function buildInputs(?object $object, string $method): array
    {
        $object ??= $this;
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties();

        $inputs = [];
        $types = [];

        foreach ($properties as $property) {
            $name = $property->getName();
            $type = $property->getType()->getName();
            $attributes = $property->getAttributes();

            if (empty($attributes)) {
                continue;
            }

            foreach ($property->getAttributes() as $attribute) {
                $attr = $attribute->getArguments();
                $attrName = $attribute->getName();

                switch ($attrName) {
                    case DTOCollections::class:
                        $inputs[$name] = [
                            self::REQUIRED => !$property->isInitialized($object),
                            self::ALLOW_EMPTY => $property->getType()->allowsNull(),
                            self::MESSAGE_EMPTY => $attr['messageEmpty'] ?? null,
                            self::MESSAGE_REQUIRED => $attr['messageRequired'] ?? null,
                        ];
                        $types[$name] = $type;

                        if ($type !== 'array' || isset($attr['name'])) {
                            [$_inputs, $_types] = $this->buildInputs(new $attr['name'], $method);

                            $types[$name] = [
                                'name' => $attr['name'],
                                'collection' => true,
                                'inputs' => $_types,
                            ];

                            $inputs[$name][self::TYPE] = self::INPUT_COLLECTION;
                            $inputs[$name][self::INPUT_FILTER] = $_inputs;
                        } else {
                            $inputs[$name][self::TYPE] = self::INPUT_ARRAY;
                            $types[$name] = 'array';
                        }

                        break;

                    case DTOParam::class:
                        $inputs[$name] = [
                            self::REQUIRED => !$property->isInitialized($object),
                            self::ALLOW_EMPTY => $property->getType()->allowsNull(),
                            self::FILTERS => [],
                            self::VALIDATORS => [],
                            self::MESSAGE_EMPTY => $attr['messageEmpty'] ?? null,
                            self::MESSAGE_REQUIRED => $attr['messageRequired'] ?? null,
                        ];
                        $types[$name] = $type;

                        if ($property->hasDefaultValue()) {
                            $inputs[$name][self::DEFAULT_VALUE] = $property->getDefaultValue();

                            if (!$inputs[$name][self::DEFAULT_VALUE]) {
                                $inputs[$name][self::ALLOW_EMPTY] = true;
                            }
                        }

                        if ($inputs[$name][self::ALLOW_EMPTY] && $property->getType()->allowsNull()) {
                            $inputs[$name][self::FILTERS][ToNull::class] = [
                                self::NAME => ToNull::class,
                            ];
                        }

                        switch ($type) {
                            case 'int':
                                $inputs[$name][self::FILTERS][ToInt::class] = [
                                    self::NAME => ToInt::class,
                                ];

                                break;
                            case 'float':
                                $inputs[$name][self::FILTERS][ToFloat::class] = [
                                    self::NAME => ToInt::class,
                                ];

                                break;
                            case 'string':
                                $inputs[$name][self::FILTERS][ToString::class] = [
                                    self::NAME => ToString::class,
                                ];

                                break;
                            case 'bool':
                                $inputs[$name][self::FILTERS][ToBool::class] = [
                                    self::NAME => ToBool::class,
                                ];

                                break;

                            case 'array':
                                $inputs[$name][self::FILTERS][ToArray::class] = [
                                    self::NAME => ToArray::class,
                                ];

                                break;

                            default:
                                if (enum_exists($type)) {
                                    $types[$name] = [
                                        'name' => $type,
                                    ];
                                    $inputs[$name][self::FILTERS][ToString::class] = [
                                        self::NAME => ToString::class,
                                    ];
                                    $inputs[$name][self::VALIDATORS][Enum::class] = [
                                        self::NAME => Enum::class,
                                        self::OPTIONS => [
                                            Enum::OPTION_ENUM => $type,
                                        ],
                                    ];

                                    if (!empty($inputs[$name][self::DEFAULT_VALUE])) {
                                        $inputs[$name][self::DEFAULT_VALUE] = $inputs[$name][self::DEFAULT_VALUE]->value;
                                    }
                                } else {
                                    [$_inputs, $_types] = $this->buildInputs(new $type, $method);

                                    if ($_inputs) {
                                        $types[$name] = [
                                            'name' => $type,
                                            'inputs' => $_types,
                                        ];

                                        $inputs[$name][self::FILTERS][ToArray::class] = [
                                            self::NAME => ToArray::class,
                                        ];
                                        $inputs[$name][self::TYPE] = self::INPUT_STRUCTURE;
                                        $inputs[$name][self::INPUT_FILTER] = $_inputs;
                                    } else {
                                        $types[$name] = [
                                            'name' => $type,
                                        ];
                                    }
                                }

                                continue 3;
                        }

                        switch ($type) {
                            case 'int':
                            case 'float':
                                if (isset($attr['min']) || isset($attr['max'])) {
                                    $inputs[$name][self::VALIDATORS][Between::class] = [
                                        self::NAME => Between::class,
                                        self::OPTIONS => [
                                            Between::OPTION_MIN => $attr['min'] ?? null,
                                            Between::OPTION_MAX => $attr['max'] ?? null,
                                        ],
                                    ];
                                }

                                break;

                            case 'string':
                                if (isset($attr['min']) || isset($attr['max'])) {
                                    $inputs[$name][self::VALIDATORS][StringLength::class] = [
                                        self::NAME => StringLength::class,
                                        self::OPTIONS => [
                                            StringLength::OPTION_MIN => $attr['min'] ?? null,
                                            StringLength::OPTION_MAX => $attr['max'] ?? null,
                                        ],
                                    ];
                                }
                                break;

                            case 'array':
                                if (isset($attr['min']) || isset($attr['max'])) {
                                    $inputs[$name][self::VALIDATORS][ArrayLength::class] = [
                                        self::NAME => ArrayLength::class,
                                        self::OPTIONS => [
                                            ArrayLength::OPTION_MIN => $attr['min'] ?? null,
                                            ArrayLength::OPTION_MAX => $attr['max'] ?? null,
                                        ],
                                    ];
                                }
                                break;
                        }

                        break;

                    case DTOValidator::class:
                        $inputs[$name][self::VALIDATORS][$attr['key'] ?? $attr['name']] = [
                            self::NAME => $attr['name'],
                            self::OPTIONS => $attr['options'] ?? [],
                        ];

                        break;
                    case DTOFilter::class:
                        $inputs[$name][self::FILTERS][$attr['name']] = [
                            self::NAME => $attr['name'],
                            self::OPTIONS => $attr['options'] ?? [],
                        ];

                        break;
                }
            }
        }

        return [$inputs, $types];
    }

    public function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_XHR']) || ($_SERVER['CONTENT_TYPE'] ?? '') === 'application/json'
            || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}