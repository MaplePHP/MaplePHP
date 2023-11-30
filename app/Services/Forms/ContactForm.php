<?php

namespace Services\Forms;

class ContactForm extends AbstractForm
{
    /**
     * This form can be loaded statically with PHP or dynamically with the Responder
     * @return void
     */
    protected function createForm(): void
    {
        $this->form->add([
            "firstname" => [
                "type" => "text",
                "label" => $this->provider->local("auth")->get("firstname", "First name"),
                "validate" => [
                    "length" => [1, 60]
                ]
            ],
            "lastname" => [
                "type" => "text",
                "label" => $this->provider->local("auth")->get("lastname", "Last name"),
                "validate" => [
                    "length" => [1, 80]
                ]
            ],
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
            "message" => [
                "type" => "textarea",
                "label" => $this->provider->local("auth")->get("message", "Message"),
                "validate" => [
                    "length" => [1, 2000]
                ]
            ]
        ]);

        /*
        // Set form values
        $this->form->setValues([
            "firstname" => "John Doe",
        ]);
         */
    }
}
