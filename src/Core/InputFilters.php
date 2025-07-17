<?php

namespace Arrtxp\Core;

use Interop\Container\ContainerInterface;

readonly class InputFilters implements ServiceConstants
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function filterParams(array $inputs, array &$params): void
    {
        foreach ($inputs as $field => $input) {
            if (!array_key_exists($field, $params)) {
                if (!array_key_exists(self::DEFAULT_VALUE, $input)) {
                    continue;
                } else {
                    $params[$field] = $input[self::DEFAULT_VALUE];
                }
            }

            $type = ($input[self::TYPE] ?? '');

            if ($type === self::INPUT_STRUCTURE && is_array($params[$field])) {
                $this->filterParams($input[self::INPUT_FILTER], $params[$field]);

                continue;
            }

            if ($type === self::INPUT_COLLECTION) {
                foreach ($params[$field] as $index => $value) {
                    $params[$field][$index] ??= [];
                    $this->filterParams($input[self::INPUT_FILTER], $params[$field][$index]);
                }

                continue;
            }

            if ($type === self::INPUT_ARRAY && is_array($params[$field])) {
                $structureInput = $input;
                unset($structureInput[self::TYPE], $structureInput[self::INPUT_FILTER]);

                $input[self::TYPE] = self::INPUT_STRUCTURE;

                $structures = [];
                foreach ($params[$field] as $index => $value) {
                    $structures[$index] = $structureInput;
                }

                $this->filterParams($structures, $params[$field]);

                continue;
            }

            /** @var Filter|array $filter */
            /** @var Filter $f */
            foreach ($input[self::FILTERS] ?? [] as $filter) {
                if (is_string($filter)) {
                    $filter = [
                        self::NAME => $filter,
                    ];
                }

                if (is_array($filter)) {
                    $f = new $filter[self::NAME]();
                } else {
                    $f = $filter;
                }

                $f->build();

                $options = [];
                foreach ($filter[self::OPTIONS] ?? [] as $key => $value) {
                    if ($value instanceof DTOOption) {
                        $options[$key] = $value->get();
                    } else {
                        $options[$key] = $value;
                    }
                }

                $f->setOptions($options);

                $params[$field] = $f->filter($params[$field]);
            }

            if (!array_key_exists($field, DTOOption::$options)) {
                DTOOption::set($field, $params[$field]);
            }
        }

        foreach ($params as $field => $value) {
            if (!isset($inputs[$field])) {
                unset($params[$field]);
            }
        }
    }

    public function validation(
        array $inputs,
        array &$params,
        array &$errors,
        string $path = ''
    ): void {
        foreach ($inputs as $field => $input) {
            $errorPath = "{$path}/{$field}";

            $type = $input[self::TYPE] ?? null;

            if ($type && (empty($params[$field]) || !is_array($params[$field]))) {
                if (!empty($input[self::REQUIRED])) {
                    $errors[$errorPath] = $input[self::MESSAGE_REQUIRED] ?? 'Pole wymagane.';
                    unset($params[$field]);
                }

                continue;
            }

            if (empty($input[self::REQUIRED]) && !empty($input[self::ALLOW_EMPTY])
                && isset($params[$field]) && is_null($params[$field])) {
                continue;
            }

            if (!empty($input[self::REQUIRED]) && !array_key_exists($field, $params)) {
                $errors[$errorPath] = $input[self::MESSAGE_REQUIRED] ?? 'Pole wymagane.';
                unset($params[$field]);

                continue;
            }

            if (empty($input[self::ALLOW_EMPTY]) && (!isset($params[$field]) || $params[$field] === '')) {
                $errors[$errorPath] = $input[self::MESSAGE_EMPTY] ?? 'Pole nie może być puste.';
                unset($params[$field]);

                continue;
            }

            if (!array_key_exists($field, $params)) {
                continue;
            }

            if ($type === self::INPUT_STRUCTURE) {
                $this->validation(
                    $input[self::INPUT_FILTER],
                    $params[$field],
                    $errors,
                    "$errorPath"
                );

                continue;
            }

            if ($type === self::INPUT_COLLECTION) {
                foreach ($params[$field] as $key => $_params) {
                    $this->validation(
                        $input[self::INPUT_FILTER],
                        $_params,
                        $errors,
                        "$errorPath/$key"
                    );
                }

                continue;
            }

            if ($type === self::INPUT_ARRAY) {
                $structureInput = $input;
                unset($structureInput[self::TYPE], $structureInput[self::INPUT_FILTER]);

                $input[self::TYPE] = self::INPUT_STRUCTURE;
                $input[self::INPUT_FILTER] = [];

                $structures = [];
                foreach ($params[$field] as $index => $value) {
                    $structures[$index] = $structureInput;
                }

                $this->validation(
                    [
                        $field => [
                            self::TYPE => self::INPUT_STRUCTURE,
                            self::INPUT_FILTER => $structures,
                        ],
                    ],
                    $params,
                    $errors,
                    "$path"
                );

                continue;
            }

            if ($input[self::ALLOW_EMPTY] && $params[$field] === null) {
                continue;
            }

            /** @var Validator|array $validator */
            /** @var Validator $v */
            foreach ($input[self::VALIDATORS] ?? [] as $validator) {
                if (is_string($validator)) {
                    $v = $this->container->get($validator);
                } elseif (is_array($validator)) {
                    $v = $this->container->get($validator[self::NAME]);
                }

                $v->build();

                $options = [];
                foreach ($validator[self::OPTIONS] ?? [] as $key => $value) {
                    if ($value instanceof DTOOption) {
                        $options[$key] = $value->get();
                    } else {
                        $options[$key] = $value;
                    }
                }

                $v->setOptions($options);

                if (!$v->isValid($params[$field], $params)) {
                    $errors[$errorPath] = current($v->getErrors());
                    unset($params[$field]);

                    if (($validator[self::OPTIONS][self::BREAK_CHAIN] ?? true)) {
                        break;
                    }
                }
            }
        }
    }
}