<?php

namespace Services\Forms;

use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Http\Interfaces\ServerRequestInterface;
use MaplePHP\Security\Csrf;
use MaplePHP\Output\Json;
use MaplePHP\Form\Validate as valid;
use Models\Form;
use Services\Forms\AbstractForm;

class Validate
{
    protected $container;
    protected $form;
    protected $csrf;
    protected $json;


    public function __construct(ContainerInterface $container, Json $json)
    {
        $this->container = $container;
        $this->csrf = new Csrf($container->get("cookies")->inst());
        $this->json = $json;
    }

    /**
     * This will validate the form
     * @param  Form|AbstractForm    $form  Instance of Form or AbstractForm
     * @param  array|object|null    $data  Request data
     * @return bool|array False or XSS protected data
     */
    public function validate(Form|AbstractForm $form, array|object|null $data)
    {
        // Request body can return (array|object|null), so convert it.
        $data = (array)$data;
        if ($form instanceof AbstractForm) {
            $form = $form->form();
        }
        $csrfToken = ($data['csrfToken'] ?? "");

        $form->build();
        if (!$this->csrf->isValid($csrfToken)) {
            $this->json->add("status", 2);
            $this->json->add("csrfToken", $this->csrf->createToken());
            $this->json->add("message", $this->container->local("validate")
                ->get("formHibernate", "The form has gone into hibernation. Click send again."));
            return false;
        }

        $validate = new valid($form->inst(), $data);
        if ($this->container->has("local")) {
            $validate->setLocal($this->container->local("validate"));
        }

        if ($error = $validate->execute()) {
            $this->json->add("status", 3);
            $this->json->add("error", [
                "form" => $error
            ]);
            return false;
        }
        return $this->container->encode($validate->getRequest())->encode()->get();
    }
}
