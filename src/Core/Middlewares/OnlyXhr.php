<?php

namespace Core\Middlewares;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OnlyXhr implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $isAjax = !empty($_SERVER['HTTP_XHR']) || ($_SERVER['CONTENT_TYPE'] ?? '') === 'application/json'
            || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

        if (!$isAjax) {
            return new EmptyResponse(403);
        }

        $referer = $request->getHeaders()['referer'][0] ?? '';
        $serverName = $request->getServerParams()['SERVER_NAME'] ?? '';

        if (!$referer || !$serverName || !str_contains($referer, $serverName)) {
            return new EmptyResponse(403);
        }

        return $handler->handle($request);
    }
}