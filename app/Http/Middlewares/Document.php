<?php

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;

class Document implements MiddlewareInterface
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
     * Add footer to the document
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function footer(ResponseInterface $response, RequestInterface $request)
    {
        // Partial in document director
        // The exclamation character will disable thrown error, if you remove the partial template file.
        $this->provider->view()->setPartial("footer.!document/footer");

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
