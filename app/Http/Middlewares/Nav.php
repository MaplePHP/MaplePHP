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
use MaplePHP\Foundation\Nav\Middleware\Navigation;

class Nav extends Navigation implements MiddlewareInterface
{
    /**
     * Before controllers
     * @return ResponseInterface
     */
    public function before()
    {
        // You can use this middleware to create a dynamic navigation
        // The id is not required, but will create it´s own id with increment, starting from 1 if not filled in. 
        // The id is used to select parent!
        $this->nav->add("main", [
            "id" => 1,
            "name" => "Start",
            "slug" => "",
            "parent" => 0,
            "title" => "Meta title start",
            "description" => "Meta description start"

        ])->add("main", [
            "id" => 2,
            "name" => "Contact",
            "slug" => "contact",
            "parent" => 0,
            "title" => "Meta title contact",
            "description" => "Meta description contact"
        ]);

        // Will build the navigation
        //parent::before();
    }

    /**
     * After controllers
     * @return void
     */
    public function after()
    {
    }

}
