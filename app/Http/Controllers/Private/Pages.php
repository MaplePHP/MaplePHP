<?php

namespace Http\Controllers\Private;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;
use Http\Controllers\BaseController;

class Pages extends BaseController
{
    public const LOGOUT_PATH = "/login";

    public function __construct(Provider $provider)
    {
    }

    /**
     * Logout
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function logout(ResponseInterface $response, RequestInterface $request)
    {
        if ($this->session()->loggedIn()) {
            unset($_SESSION);
            session_destroy();
            $this->cookies()->delete("maple_token_1");
        }
        $response->location($this->url()->getRoot(static::LOGOUT_PATH));
    }

    /**
     * Profile
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return void
     */
    public function profile(ResponseInterface $response, RequestInterface $request)
    {
        $this->view()->setPartial("main.ingress", [
            "tagline" => getenv("APP_NAME"),
            "name" => "Welcome " . $this->user()->firstname,
            "content" => "Get ready to build you first application."
        ]);
    }
}
