<?php

namespace Models\Forms;

use MaplePHP\Foundation\Form\Forms\AbstractForm;

class ContactForm extends AbstractForm
{
    /**
     * createForm method is an required, abstract method used to build form
     * @return void
     */
    protected function createForm(): void
    {
        $this->form->add([
            "firstname" => [
                "type" => "text",
                "label" => "First name",
                "validate" => [
                    "length" => [1, 60]
                ]
            ],
            "lastname" => [
                "type" => "text",
                "label" => "Last name",
                "validate" => [
                    "length" => [1, 80]
                ]
            ],
            "email" => [
                "type" => "text",
                "label" => "Email",
                "attr" => [
                    "type" => "email"
                ],
                "validate" => [
                    "length" => [1, 160]
                ]
            ],
            "message" => [
                "type" => "textarea",
                "label" => "Message",
                "validate" => [
                    "length" => [1, 2000]
                ]
            ]
        ]);
    }
}