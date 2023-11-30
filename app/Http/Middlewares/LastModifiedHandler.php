<?php

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;

class LastModifiedHandler implements MiddlewareInterface
{
    public function __construct()
    {
    }

    /**
     * Start prepared session
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function before(ResponseInterface $response, RequestInterface $request): void
    {
    }

    /**
     * After controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface
     */
    public function after(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        // Response lastModified method sets BEFORE in conntroller then executed here
        if ($modDate = $response->getModDate()) {
            $ifModifiedSince = $request->getHeaderLine("if-modified-since");
            if (is_string($ifModifiedSince) && strtotime($ifModifiedSince) >= $modDate) {
                $response->withStatus(304)->executeHeaders();
                exit;
            }
        }
        return $response;
    }
}
