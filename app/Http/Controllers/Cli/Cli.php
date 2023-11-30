<?php

namespace Http\Controllers\Cli;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use Http\Controllers\Cli\CliInterface;
use Services\Stream\Cli as Stream;

class Cli implements CliInterface
{
    protected $container;
    protected $args;
    protected $cli;

    public function __construct(ContainerInterface $container, RequestInterface $request, Stream $cli)
    {
        $this->container = $container;
        $this->args = $request->getCliArgs();
        $this->cli = $cli;
    }

    public function handleMissingType()
    {
        $this->cli->write("Cli [type] does not exist.");
        return $this->cli->getResponse();
    }

    public function help()
    {
        $this->cli->write('$ [PackageName] [Type] [--Value=1, --Value=2, ...]' . "\n");
        $this->cli->write('Example:');
        $this->cli->write('$ php cli config install --type=mail');
        $this->cli->write('$ php cli migrate read --table=users');
        return $this->cli->getResponse();
    }
}
