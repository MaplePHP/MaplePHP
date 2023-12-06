<?php

namespace Http\Controllers\Examples;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Http\Interfaces\UrlInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Output\Json;
use MaplePHP\Foundation\Nav\Navbar;

class DynamicPages
{
    protected $url;
    protected $json;
    protected $container;
    protected $nav;
    protected $validateRequest;

    public function __construct(ContainerInterface $container, UrlInterface $url, Json $json, Navbar $nav)
    {
        $this->url = $url;
        $this->container = $container;
        $this->json = $json;

        // Build navigation
        $this->nav = $nav->get();

        // Load http request
        $this->validateRequest = $nav->validate($this->url->withType(["pages"])->getVars());

        // Show nav / or in DomManipulation middleware
        $this->container->get("view")->setPartial("navigation", [
            "nav" => $this->nav
        ]);
    }

    /**
     * Load dynamic page
     * @Route[GET:/{page:[^/]+}]
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function pages(ResponseInterface $response): ResponseInterface
    {
        // Validate dynamic page
        if ($this->validateRequest->status() !== 200) {
            $this->container->get("head")->getElement("title")->setValue("404 Could not find the page");
            $this->container->get("head")->getElement("description")->attr("content", "404 Could not find the page");
            return $response->withStatus(404);
        }

        $this->container->get("head")->getElement("title")->setValue("Lorem ipsum");
        $this->container->get("head")->getElement("description")->attr("content", "Changed!");
        // Database values
        $this->container->get("view")->setPartial("breadcrumb", [
            "name" => "Some data",
            "content" => "Some data from the database"
        ]);

        return $response;
    }
}
