<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
use MaplePHP\Core\App;
use MaplePHP\Http;
use MaplePHP\Handler;
use MaplePHP\Container\Container;

$dir = realpath(dirname(__FILE__) . '/..') . '/';
require_once("{$dir}app/Core/autoload.php");

$stream = new Http\Stream(Http\Stream::TEMP);
$response = new Http\Response($stream);
$env = new Http\Environment();
$request = new Http\ServerRequest(new Http\Uri($env->getUriParts([
    "dir" => $dir
])), $env);

$container = new Container();
$routes = new Handler\RouterDispatcher($response, $request);
$emitter = new Handler\Emitter($container);
$app = new App($emitter, $routes);

$app->enablePrettyErrorHandler();
$app->setContainer($container);
$app->enableTemplateEngine(true);
$app->excludeRouterFiles(["cli"]);

//$emitter->errorHandler(false, false, true, "{$dir}storage/logs/error.log");
// bool $displayError, bool $niceError, bool $logError, string $logErrorFile
//$emitter->errorHandler(true, true, true, "{$dir}storage/logs/error.log");

// Set current URI path
$param = $request->getQueryParams();
$routes->setDispatchPath("/" . ($param['page'] ?? ""));
$app->run();
