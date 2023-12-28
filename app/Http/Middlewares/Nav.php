<?php
/**
 * Create a dynamic and custom navigation
 * 
 * You can also create a navigation model and extend to "MaplePHP\Foundation\Nav\Navbar"
 * the overwrite the methods in it to create a custom navbar
 * Take a look at the file Foundation/Nav/Navbar.php in your vendor directory.
 */

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;
use MaplePHP\Foundation\Nav\Middleware\Navigation;
use MaplePHP\Foundation\Nav\Navbar;

class Nav extends Navigation implements MiddlewareInterface
{
    protected $provider;
    protected $nav;

    public function __construct(Provider $provider, Navbar $nav)
    {
        $this->provider = $provider;
        $this->nav = $nav;
    }

    /**
     * Before controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface|void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {

        // You can use this middelware to create an dynamic navigation
        // The id is not required, but will create it´s own id with increment, starting from 1 if not filled in. 
        // The id is used to select parent!
        $this->nav->add([
            "id" => 1,
            "name" => "Start",
            "slug" => "",
            "parent" => 0,
            "title" => "Meta title start",
            "description" => "Meta description start"
            
        ])->add([
            "id" => 2,
            "name" => "Contact",
            "slug" => "contact",
            "parent" => 0,
            "title" => "Meta title contact",
            "description" => "Meta description contact"
        ]);

        // Will build the navigation
        return parent::before($response, $request);
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
