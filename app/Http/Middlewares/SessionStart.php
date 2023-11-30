<?php

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Security\Session;

class SessionStart implements MiddlewareInterface
{
    private $url;
    private $container;
    private $json;

    public function __construct(RequestInterface $request, ContainerInterface $container)
    {
        $this->container = $container;
        // Config and prepare session
    }

    /**
     * Start prepared session Before controllers method view but after controllers construct
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {
        $session = new Session(
            "maple",
            (int)getenv("SESSION_TIME"),
            "/",
            $request->getUri()->getHost(),
            ((int)getenv("SESSION_SSL") === 1),
            true
        );
        $this->container->set("session", $session);
        $this->container->get("session")->start();
    }

    /**
     * After controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function after(ResponseInterface $response, RequestInterface $request)
    {
    }
}
