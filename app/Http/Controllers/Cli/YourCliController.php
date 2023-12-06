<?php

namespace Http\Controllers\Cli;

use MaplePHP\Foundation\Cli\Connectors\CliInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Cli\StandardInput;

class YourCliController implements CliInterface
{
    protected $args;
    protected $cli;

    public function __construct(RequestInterface $request, StandardInput $cli)
    {
        $this->args = $request->getCliArgs();
        $this->cli = $cli;
    }

    public function read(): ResponseInterface
    {
        $this->cli->write("Hello World");
        return $this->cli->getResponse();
    }

    public function install(): ResponseInterface
    {
        $this->cli->write("Hello World");
        return $this->cli->getResponse();
    }

    public function help(): ResponseInterface
    {
        $this->cli->write("Help me text");  
        return $this->cli->getResponse();
    }
}