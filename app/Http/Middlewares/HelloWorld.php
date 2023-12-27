<?php

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;

class HelloWorld implements MiddlewareInterface
{
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Will load before the controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {
        // Bind array data to the provider/container.
        $this->provider->set("helloWorld", [
            "tagline" => getenv("APP_NAME"),
            "name" => "Hello world",
            "content" => "The HelloWord middleware has taking over the ingress view."
        ]);
        // You can now access the helloWorld data in your controller with "$this->provider->helloWorld()"
    }

    /**
     * Custom Before middleware (Will load before the controllers)
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function yourCustomMethod(ResponseInterface $response, RequestInterface $request)
    {
    }

    /**
     * Will load after the controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function after(ResponseInterface $response, RequestInterface $request)
    {
        // This will take over the ingress view in at partial main location.
        $this->provider->view()->setPartial("main.ingress", $this->provider->helloWorld());
    }
}
