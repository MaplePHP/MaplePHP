<?php

namespace Http\Middlewares;

use MaplePHP\Foundation\Http\Provider;
use MaplePHP\Foundation\Nav\Navbar;
use MaplePHP\Handler\Interfaces\MiddlewareInterface;

class Document implements MiddlewareInterface
{
    private Provider $provider;
    private Navbar $nav;

    public function __construct(Provider $provider, Navbar $nav)
    {
        $this->provider = $provider;
        $this->nav = $nav;
    }

    /**
     * Will load before the controllers
     * @return void
     */
    public function before(): void
    {
    }

    /**
     * Add head to the document
     * @return void
     */
    public function head(): void
    {
        // Partial in document director
        // The exclamation character will disable thrown error, if you remove the partial template file.
        $this->provider->view()->setPartial("head.!document/head");
    }

    /**
     * Add head to the document
     * @return void
     */
    public function navigation(): void
    {
        // Partial in document director
        // The exclamation character will disable thrown error, if you remove the partial template file.
        $this->provider->view()->setPartial("navigation.!document/navigation", [
            "nav" => $this->nav->get()
        ]);
    }

    /**
     * Add footer to the document
     * @return void
     */
    public function footer(): void
    {
        // Partial in document director
        // The exclamation character will disable thrown error, if you remove the partial template file.
        $this->provider->view()->setPartial("footer.!document/footer", [
            "nav" => $this->nav->get()
        ]);

    }

    /**
     * Will load after the controllers
     * @return void
     */
    public function after(): void
    {
    }
}
