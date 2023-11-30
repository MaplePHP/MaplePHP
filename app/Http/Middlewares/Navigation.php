<?php

namespace Http\Middlewares;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use Models\Navbar;

class Navigation implements MiddlewareInterface
{
    private $container;
    private $nav;

    public function __construct(ContainerInterface $container, Navbar $nav)
    {
        $this->container = $container;
        $this->nav = $nav;
    }

    /**
     * Before controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {
        // Set navigate view partial
        $this->container->get("view")->setPartial("navigation", [
            "nav" => $this->nav->get()
        ]);
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
