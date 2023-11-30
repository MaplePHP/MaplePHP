<?php

namespace Http\Controllers;

use MaplePHP\Container\Interfaces\ContainerInterface;
use Services\ServiceProvider;
use BadMethodCallException;

abstract class BaseController
{
    /**
     * This will make shortcuts to container.
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public function __call(string $method, array $args): mixed
    {
        if ($method === "has") {
            return call_user_func_array([ServiceProvider::$container, $method], $args);
        }
        if ((ServiceProvider::$container instanceof ContainerInterface) && ServiceProvider::$container->has($method)) {
            return ServiceProvider::$container->get($method, $args);
        } else {
            throw new BadMethodCallException('The method "' . $method . '" does not exist in the Container or the Class "' . static::class . '"!', 1);
        }
    }
}
