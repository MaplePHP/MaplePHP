<?php
/**
 * Create your own custom cli commands
 * @var object $routes
 */

$routes->group("command", function ($routes) {
    // It is recommended to add this handle at the begining of every grouped call
    $routes->cli("[/help]", ['Http\Controllers\Cli\YourCliController', "help"]);
    $routes->cli("/read", ['Http\Controllers\Cli\YourCliController', "read"]);
    $routes->cli("/install", ['Http\Controllers\Cli\YourCliController', "install"]);
});