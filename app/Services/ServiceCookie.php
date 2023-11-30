<?php

namespace Services;

use MaplePHP\Http\Interfaces\UrlInterface;
use MaplePHP\Http\Cookies;
use BadMethodCallException;

class ServiceCookie
{
    private $url;
    private $cookies;

    /**
     * Recommended standard settings
     * If you want a more loose cookie then do it from a new instance
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
        //path, domain, secure, httponly
        $this->cookies = new Cookies("/", $this->url->getUri()->getHost(), true, true);
        // Only read modify on same site
        $this->cookies->setSameSite("Strict");
    }

    public function inst()
    {
        return $this->cookies;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->cookies, $method)) {
            return call_user_func_array([$this->cookies, $method], $args);
        } else {
            throw new BadMethodCallException('The method "' . $method . '" does not exist in the Cookies Class!', 1);
        }
    }
}
