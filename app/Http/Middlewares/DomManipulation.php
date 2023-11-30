<?php

namespace Http\Middlewares;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Output\Dom\Document;
use MaplePHP\Output\Json;
use Services\ServiceProvider;

class DomManipulation implements MiddlewareInterface
{
    private $provider;
    private $json;

    public function __construct(ServiceProvider $provider, Json $json)
    {
        $this->provider = $provider;
        $this->json = $json;
    }

    /**
     * Before controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {
        $activeResult = false;
        $myAppName = $this->provider->env("APP_NAME", "My App");
        if ($this->provider->has("protocol")) {
            // Build a dynamic meta title with app name and the navigation items title object
            $activeResult = $this->provider->protocol()->getActiveData();
        }
        $title = ($this->provider->has("protocol")) ? ($activeResult ? $activeResult->title : false) : false;
        $title = (is_string($title)) ? "{$title} | " . $myAppName : $myAppName;

        // Configure head meta tags
        $this->provider->set("head", Document::dom("head"));
        $dom = $this->provider->head();
        $dom->bindTag("title", "title")->setValue($title);
        $dom->bindTag("meta", "description")->attr("name", "Description")->attr("content", $this->provider->env("APP_DESCRIPTION"));

        // Configure foot tags: Use primary for configs
        $this->provider->set("foot", Document::dom("foot"));
        $dom = $this->provider->foot();
        $dom->bindTag("script", "config")->attr("nonce", $this->provider->env("NONCE"));
    }

    /**
     * After controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function after(ResponseInterface $response, RequestInterface $request)
    {
        // Set the config here in before just to make sure it is already set in the services and controllers
        $this->provider->foot()->getElement("config")
        ->setValue("const NONCE = '" . $this->provider->env("NONCE") . "', CONFIG = " . $this->json->encode());
    }
}
