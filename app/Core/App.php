<?php

declare(strict_types=1);

namespace MaplePHP\Core;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Http\Interfaces\UrlInterface;
use Whoops\Handler\HandlerInterface;
use MaplePHP\Core\AppConfigs;
use MaplePHP\Handler\Emitter;
use MaplePHP\Handler\RouterDispatcher;
use MaplePHP\Http\Dir;
use MaplePHP\Http\Env;
use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Format\Local;
use MaplePHP\Container\Reflection;
use MaplePHP\Query\Connect;

class App extends AppConfigs
{
    protected Emitter $emitter;
    protected RouterDispatcher $dispatcher;
    protected ?object $whoops = null;

    private $installed = false;

    public function __construct(Emitter $emitter, RouterDispatcher $dispatcher)
    {
        $this->emitter = $emitter;
        $this->dispatcher = $dispatcher;
        $this->dir = new Dir($this->dispatcher->request()->getUri()->getDir());
    }
    
    /**
     * Setup a MySql Connection
     * @return void
     */
    protected function setupMysqlConnection(): void
    {
        $connect = $this->getenv("MYSQL_HOST");
        $database = $this->getenv("MYSQL_DATABASE");
        if ($this->hasDBEngine && $connect && $database) {
            $connect = new Connect(
                $connect,
                $this->getenv("MYSQL_USERNAME"),
                $this->getenv("MYSQL_PASSWORD"),
                $database
            );
            $connect->setCharset($this->getenv("MYSQL_CHARSET"));
            $connect->setPrefix($this->getenv("MYSQL_PREFIX"));
            $connect->execute();
        }
    }

    /**
     * Setup configs and install env
     * @return void
     */
    protected function setupConfig(): void
    {
        // LOAD env from the .env file first.
        $file = $this->dir->getRoot() . ".env";

        $this->attr['NONCE'] = bin2hex(random_bytes(16));
        $this->attr['APP_DIR'] = $this->dir->getRoot();
        //$this->container->set("nonce", $this->attr);

        $env = new Env();
        if (is_file($file)) {
            $this->installed = true;
            $env->loadEnvFile($file);
            $env->putenvArray($this->attr + $this->getConfigFileData());
            $this->attr += $env->getData();
        } else {
            // Create installation screen
            $put = $this->getConfigFileData();
            $put['config']['routers']['load'] = ["cli"];
            unset($put['config']['mysql']);
            $env->putenvArray($this->attr + $put);

            //$response, $request
            $this->dispatcher->get("/", function () {
                $this->container->get("view")->setPartial("breadcrumb", [
                    "tagline" => "Welcome to MaplePHP",
                    "name" => "Install the application",
                    "content" => "You need to first install the application in order to use it. Execute the command bellow in you command line:<br><strong>php cli config install --type=app</strong>"
                ]);
            });
        }

        // Set default envars, which can be used in config files!
        $env->execute();
        $this->attr = array_merge($this->attr, $env->getData());
    }

    /**
     * Setup Routers
     * @return void
     */
    protected function setupRouters(): void
    {
        if (($config = $this->getConfig("routers"))) {
            if ((bool)($config['cache'] ?? false)) {
                $dir = $this->getConfigDir($config['cacheFile']['path']);
                $file = $config['cacheFile']['prefix'] . $config['cacheFile']['file'];
                $this->dispatcher->setRouterCacheFile("{$dir}{$file}", false);
            }

            $routerFiles = is_null($this->routerFiles) ? ($config['load'] ?? null) : $this->routerFiles;
            if (is_array($routerFiles)) {
                foreach ($routerFiles as $file) {
                    if (!in_array($file, $this->exclRouterFiles)) {
                        $dir = $this->dir->getRoot() . "app/Http/Routes/";
                        $this->includeRoutes($this->dispatcher, "{$dir}{$file}.php");
                    }
                }
            }
        }
    }

    protected function includeRoutes(RouterDispatcher $routes, string $fullPathToFile): void
    {
        if (!is_file($fullPathToFile)) {
            throw new \Exception("The file \"{$fullPathToFile}\" do not exist. Make sure it is in the right directory!", 1);
        }
        require_once($fullPathToFile);
    }

    /**
     * Setup languages
     * @return void
     */
    protected function setupLang(): void
    {
        if ($appLang = getenv("APP_LANG")) {
            Local::setLang($appLang);
        }

        $appLangDir = getenv("APP_LANG_DIR");
        $this->setLangDir(($appLangDir) ? $appLangDir : $this->dir->getRoot() . "resources/lang/");

        // Re-set varible, might have changed above
        if ($appLangDir = getenv("APP_LANG_DIR")) {
            Local::setDir($appLangDir);
        }
    }

    /**
     * Setup view
     * @return void
     */
    protected function setupViews(): void
    {
        if ($this->hasTempEngine) {
            $this->emitter->view()
            ->setIndexDir($this->dir->getRoot() . "resources/")
            ->setViewDir($this->dir->getRoot() . "resources/views/")
            ->setPartialDir($this->dir->getRoot() . "resources/partials/")
            ->bindToBody(
                "httpStatus",
                Arr::value($this->dispatcher->response()::PHRASE)->unset(200, 201, 202)->arrayKeys()->get()
            )
            ->setIndex("index")
            ->setView("main");
        }
    }

    /**
     * Setup the dispatcher
     * @return void
     */
    protected function setupDispatch(?callable $call = null): void
    {
        $this->dispatcher->dispatch(function (
            int $dispatchStatus,
            ResponseInterface &$response,
            RequestInterface $request,
            UrlInterface $url
        ) use ($call): ResponseInterface {
            switch ($dispatchStatus) {
                case RouterDispatcher::NOT_FOUND:
                    $response = $response->withStatus(404);
                    break;
                case RouterDispatcher::METHOD_NOT_ALLOWED:
                    $response = $response->withStatus(403);
                    break;
                case RouterDispatcher::FOUND:
                    $this->defaultInterfaces($response, $request, $url);
                    if (is_callable($call)) {
                        $response = $call($response);
                    }
                    break;
            }
            return $response;
        });
    }

    /**
     * Add a class that will where it's instance will be remembered through the app and its
     * controllers, To do this, you must first create an interface of the class, which will
     * become its uniqe identifier.
     * @return void
     */
    final protected function defaultInterfaces($response, $request, $url): void
    {
        Reflection::interfaceFactory(function ($className) use ($request, &$response, $url) {
            switch ($className) {
                case "UrlInterface":
                    return $url;
                case "DirInterface":
                    return $this->dir;
                case "ContainerInterface":
                    return $this->container;
                case "RequestInterface":
                    return $request;
                case "ResponseInterface":
                    return $response;
                default:
                    return null;
            }
        });
    }

    /**
     * Setup error handling, enables with APP_DEBUG
     * @psalm-suppress InvalidArgument
     * @return void
     */
    protected function setupErrorHandler(): void
    {
        if (getenv("APP_DEBUG")) {
            if (!is_null($this->whoops) || (class_exists('\Whoops\Run') && !is_null($this->errorHandler))) {
                $class = "\\Whoops\\Handler\\{$this->errorHandler}";
                if (is_null($this->whoops)) {
                    $this->whoops = new \Whoops\Run();
                    $this->whoops->pushHandler($this->getWhoopsHandler($class));
                    $this->whoops->register();
                } else {
                    if (!$this->hasWhoopsHandler($class)) {
                        $this->whoops->pushHandler($this->getWhoopsHandler($class));
                        $this->whoops->register();
                    }
                }
            } else {
                $this->emitter->errorHandler(true, true, true, $this->dir->getRoot() . "storage/logs/error.log");
            }
        }
    }

    /**
     * Set response headers
     * @return ResponseInterface
     */
    protected function setupHeaders($response): ResponseInterface
    {
        if ($ttl = (int)getenv("APP_CACHE_TTL")) {
            $this->emitter->setDefaultCacheTtl($ttl);
        }
        if (isset($this->attr['config']['headers'])) {
            foreach ($this->attr['config']['headers'] as $key => $value) {
                if (!$response->hasHeader($key)) {
                    $response = $response->withHeader($key, $value);
                }
            }
        }

        return $response;
    }

    /**
      * Run the application
      * @return void
      */
    public function run(): void
    {
        $this->setupConfig();
        $this->setupErrorHandler();
        $this->setupMysqlConnection();
        $this->setupViews();
        $this->setupRouters();
        $this->setupLang();

        $this->setupDispatch(function ($response) {
            return $this->setupHeaders($response);
        });

        $response = $this->dispatcher->response();
        $request = $this->dispatcher->request();

        if ((int)getenv("APP_SSL") === 1 && !$request->isSSL()) {
            $location = $request->getUri()->withScheme("https")->withQuery("")->withPort(null)->getUri();
            $response->withStatus(301)->location($location);
        }

        if (!$this->container->has("url")) {
            $this->container->set("url", $this->dispatcher->url());
            if($this->installed === false) {
                $this->container->set("TempServiceUrl", '\Services\ServiceUrl');
                $this->dispatcher->url()->setHandler($this->container->get('TempServiceUrl'));
            }
        }

        if (!($response instanceof ResponseInterface)) {
            throw new \Exception("Fatal error: The apps ResponseInterface has not been initilized!", 1);
        }
        
        $type = $response->getHeaderLineData("content-type");
        switch (($type[0] ?? "text/html")) {
            case "text/html":
                $this->enablePrettyErrorHandler();
                break;
            case "application/json":
                $this->enableJsonErrorHandler();
                break;
            default:
                $this->enablePlainErrorHandler();
        }


        // Handle error response IF contentType has changed!
        $this->setupErrorHandler();

        // If you set a buffered response string it will get priorities agains all outher response
        $this->emitter->outputBuffer($this->dispatcher->getBufferedResponse());
        $this->emitter->run($response, $request);
    }
}
