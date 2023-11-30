<?php

namespace Http\Middlewares;

use MaplePHP\Handler\Interfaces\MiddlewareInterface;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Http\Interfaces\UrlInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use Services\ServiceProvider;
use Models\Users;
use MaplePHP\Security\Token;

class LoggedIn implements MiddlewareInterface
{
    public const USER_COLUMNS = "id,firstname,lastname,email";
    public const LOGIN_PATH = "/profile";
    public const LOGOUT_PATH = "/login";

    private $url;
    private $container;
    private $users;

    public function __construct(
        ContainerInterface $container,
        UrlInterface $url,
        Users $users
    ) {
        $this->container = $container;
        $this->url = $url;
        $this->users = $users;
    }

    /**
     * Before controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function before(ResponseInterface $response, RequestInterface $request)
    {

        $session = $this->container->get("session");
        $cookies = $this->container->get("cookies");
        $database = $this->container->get("DB");
        $date = $this->container->get("date");

        $token = new Token(Token::REMEMBER, $cookies->inst(), $database, $date);
        if (!$session->loggedIn() && ($userID = $token->validate()) && is_int($userID)) {
            // Refresh token every time session is destroyed
            $token->generate($userID);
            $session->setLogin($userID);
        }
    }

    /**
     * Show if logged out, if middleware method is specified in router
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface
     */
    public function publicZone(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        if ($this->container->get("session")->loggedIn()) {
            // Will kill script and then redirect
            $response->location($this->url->getRoot(static::LOGIN_PATH));
        }

        return $response;
    }

    /**
     * Show if logged in, if middleware method is specified in router
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface
     */
    public function privateZone(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        if (!$this->container->get("session")->loggedIn()) {
            // Will kill script and then redirect
            $response->location($this->url->getRoot(static::LOGOUT_PATH));
        }

        if ($obj = $this->users->getUserById($this->container->get("session")->getByKey())) {
            $this->container->set("user", $obj);
        } else {
            return $this->logout($response, $request);
        }

        return $response;
    }

    /**
     * Logout (This method could be duplicated to controller)
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface
     */

    public function logout(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        if ($this->container->get("session")->loggedIn()) {
            unset($_SESSION);
            session_destroy();
        }
        $response->location($this->url->getRoot(static::LOGOUT_PATH));
        return $response;
    }


    /**
     * After controllers
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function after(ResponseInterface $response, RequestInterface $request): void
    {
    }
}
