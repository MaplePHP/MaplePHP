<?php

namespace Services;

use MaplePHP\Log\Interfaces\LoggerInterface;
use MaplePHP\Log\Logger;
use MaplePHP\Log\Handlers\StreamHandler;
use MaplePHP\Http\Interfaces\DirInterface;

class ServiceLogger
{
    public const MAX_SIZE = 5000; //KB
    public const MAX_COUNT = 10;

    private $handler;
    private $logger;

    public function __construct(DirInterface $dir)
    {
        if (is_null($this->logger)) {
            $this->handler = new StreamHandler($dir->getLogs("logger.log"), static::MAX_SIZE, static::MAX_COUNT);
            $this->logger = new Logger($this->handler);
        }
    }

    public function get(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Access logger
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @psalm-taint-sink
     */
    public function __call($method, $args): mixed
    {
        if (method_exists($this->logger, $method)) {
            return call_user_func_array([$this->logger, $method], $args);
        } else {
            throw new \BadMethodCallException("The method ({$method}) does not exist in \"".__CLASS__."\" (DirInterface or DirHandlerInterface).", 1);
        }
    }

    
}
