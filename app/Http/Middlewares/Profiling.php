<?php

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Query\Connect;

class Profiling implements MiddlewareInterface
{

    public function __construct()
    {
    }

    /**
     * Start prepared session Before controllers method view but after controllers construct
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {
        Connect::startProfile();
    }

    /**
     * After controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function after(ResponseInterface $response, RequestInterface $request)
    {
        $profile = Connect::endProfile();
        if (is_string($profile)) {
            $response->getBody()->write($profile);
        } else {
            $response->getBody()->write("Nothing to report");
        }
    }
}
