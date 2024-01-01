<?php

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;
use MaplePHP\Foundation\Nav\Navbar;

class Document implements MiddlewareInterface
{
    private $provider;
    private $nav;

    public function __construct(Provider $provider, Navbar $nav)
    {
        $this->provider = $provider;
        $this->nav = $nav;
    }

    /**
     * Will load before the controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {
    }

    /**
     * Add head to the document
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function head(ResponseInterface $response, RequestInterface $request)
    {
        // Partial in document director
        // The exclamation character will disable thrown error, if you remove the partial template file.
        $this->provider->view()->setPartial("head.!document/head");

    }

    /**
     * Add head to the document
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function navigation(ResponseInterface $response, RequestInterface $request)
    {
        // Partial in document director
        // The exclamation character will disable thrown error, if you remove the partial template file.
        $this->provider->view()->setPartial("navigation.!document/navigation", [
            "nav" => $this->nav->get()
        ]);
    }

    /**
     * Add footer to the document
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function footer(ResponseInterface $response, RequestInterface $request)
    {
        // Partial in document director
        // The exclamation character will disable thrown error, if you remove the partial template file.
        $this->provider->view()->setPartial("footer.!document/footer", [
            "nav" => $this->nav->get()
        ]);

    }

    /**
     * Will load after the controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function after(ResponseInterface $response, RequestInterface $request)
    {

    }
}
