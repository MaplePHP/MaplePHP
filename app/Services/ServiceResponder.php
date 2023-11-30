<?php

namespace Services;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Output\Json;
use MaplePHP\DTO\Traverse;

class ServiceResponder
{
    private $response;
    private $json;

    /**
     * Use @ServiceResponder to communicate with app frontend code
     * @param ResponseInterface  $response
     * @param Json               $json
     */
    public function __construct(ResponseInterface $response, Json $json)
    {
        $this->response = $response;
        $this->json = $json;
    }

    /**
     * Access json instance
     * @return Json
     */
    public function json(): Json
    {
        return $this->json;
    }

    /**
     * Add custom data to responder
     * @param string $key
     * @param mixed  $data
     */
    public function add(string $key, mixed $data): self
    {
        $this->json->add($key, $data);
        return $this;
    }

    /**
     * Set form values
     * @param array $values
     */
    public function setValues(array $values): self
    {
        $this->json->mergeTo("values", $values);
        return $this;
    }

    /**
     * Show message modal
     * @param  string $textA
     * @param  string|null $textB
     * @return self
     */
    public function message(?string $textA, ?string $textB = null): self
    {
        $this->json->add("status", 1);
        if (is_null($textB)) {
            $this->json->add("message", $textA);
        } else {
            if (!is_null($textA)) {
                $this->json->add("headline", $textA);
            }
            $this->json->add("message", $textB);
        }
        return $this;
    }

    /**
     * Show error modal
     * @param  string $textA
     * @param  string|null $textB
     * @return self
     */
    public function error(?string $textA, ?string $textB = null): self
    {
        $this->json->add("status", 2);
        if (is_null($textB)) {
            $this->json->add("message", $textA);
        } else {
            if (!is_null($textA)) {
                $this->json->add("headline", $textA);
            }
            $this->json->add("message", $textB);
        }
        return $this;
    }

    /**
     * Redirect to
     * @param  string $redirect URL
     * @return self
     */
    public function redirect(string $redirect)
    {
        $this->json->add("status", 4);
        $this->json->add("redirect", $redirect);
        return $this;
    }

    /**
     * Reload current page
     * @return self
     */
    public function reload(): self
    {
        $this->json->add("status", 4);
        return $this;
    }

    /**
     * Show OK modal and reload the page on callback
     * @param  string $message
     * @return self
     */
    public function okReload(string $message): self
    {
        $this->json->add("status", 5);
        $this->json->add("type", "ok");
        $this->json->add("message", $message);
        return $this;
    }

    /**
     * Show OK modal and redirect to URL on callback
     * @param  string $message
     * @param  string $redirect URL
     * @return self
     */
    public function okRedirect(string $message, string $redirect): self
    {
        $this->json->add("status", 5);
        $this->json->add("type", "ok");
        $this->json->add("message", $message);
        $this->json->add("redirect", $redirect);
        return $this;
    }

    /**
     * Show Confirm modal and reload the page on [OK] callback or do nothing on [CANCEL]
     * @param  string $message
     * @return self
     */
    public function confirmReload(string $message): self
    {
        $this->json->add("status", 5);
        $this->json->add("type", "confirm");
        $this->json->add("message", $message);
        return $this;
    }

    /**
     * Show Confirm modal and redirect to URL on [OK] callback or do nothing on [CANCEL]
     * @param  string $message
     * @param  string $redirect URL
     * @return self
     */
    public function confirmRedirect(string $message, string $redirect): self
    {
        $this->json->add("status", 5);
        $this->json->add("type", "confirm");
        $this->json->add("message", $message);
        $this->json->add("redirect", $redirect);
        return $this;
    }

    /**
     * Clear the cache respone
     * @return self
     */
    public function clearCache(): self
    {
        $this->response = $this->response->clearCache();
        return $this;
    }

    /**
     * Build response
     * @return ResponseInterface
     */
    public function build(): ResponseInterface
    {

        $this->response = $this->response->withHeader("Content-type", "application/json; charset=UTF-8");
        $this->response->getBody()->seek(0);
        $this->response->getBody()->write((string)$this->json->encode());
        return $this->response;
    }

    public function setView(string|array $identifier, array $data = [])
    {

        $element = null;
        $key = $identifier;
        if (is_array($identifier)) {
            $key = key($identifier);
            $element = ($identifier[$key] ?? null);
        }

        $json = clone $this->json;
        $json->reset([]);

        $this->json->mergeTo("views", [
            [
                "type" => $key,
                "element" => $element,
                "data" => $data,
                "part" => $json
            ]
        ]);

        return $json;
    }

    /*

    "data" => [
        "headline" => "Dynamic view",
        "content" => "This is a dynamic view loaded through the responder library."
    ],
    "config" => [
        "controls" => false,
        "nestedNames" => false
    ],
    "fields" => [
        "ingress" => [
            "type" => "ingress",
            "data" => [
                "headline" => "<strong>Lorem</strong> ipsum dolor 1",
                "content" => "Lorem ipsum dolor"
            ],
        ],
        "password" => [
            "type" => "text",
            "label" => "Password",
        ],
        "group" => [
            "type" => "group",
            "fields" => [
                "text" => [
                    "type" => "text",
                    "label" => "wwdqw"
                ]
            ]
        ],
        "gdpr" => [
            "type" => "checkbox",
            "items" => [
                1 => "Jag godkänner gdpr blablabla..."
            ]
        ]
    ]
     */
}
