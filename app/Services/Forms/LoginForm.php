<?php

namespace Services\Forms;

class LoginForm extends AbstractForm
{
    /**
     * This form can be loaded statically with PHP or dynamically with the Responder
     * @return void
     */
    protected function createForm(): void
    {
        $this->add([
            "email" => [
                "type" => "text",
                "label" => $this->provider->local("auth")->get("email", "Email"),
                "attr" => [
                    "type" => "email"
                ],
                "validate" => [
                    "length" => [1, 160]
                ]
            ],
            "password" => [
                "type" => "text",
                "label" => $this->provider->local("auth")->get("password", "Password"),
                "attr" => [
                    "type" => "password"
                ],
                "validate" => [
                    "length" => [1, 60]
                ]
            ],
            "remember" => [
                "type" => "checkbox",
                "items" => [
                    1 => $this->provider->local("auth")->get("remember-me", "Remember me")
                ]
            ]
        ]);
    }
}
