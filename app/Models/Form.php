<?php

declare(strict_types=1);

namespace Models;

use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Form\Fields;
use MaplePHP\Security\Csrf;
use Services\Forms\FormFields;
use BadMethodCallException;

class Form
{
    public const FORM_NAME = null;

    protected $form;
    protected $csrf;
    private $key;

    /**
     * Form modal will combine all essentials libraries
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, FormFields $FormFields)
    {
        $this->form = new Fields($FormFields);
        $this->csrf = new Csrf($container->get("cookies")->inst());
    }

    /**
     * Get form instance
     * @return Fields
     */
    public function inst(): Fields
    {
        return $this->form;
    }

    /**
     * Will create an hidden CSRF token field
     * @return string
     */
    public function getTokenTag(): string
    {
        return $this->csrf->tokenTag();
    }

    /**
     * Will create an hidden CSRF token field
     * @return string
     */
    public function getToken(): string
    {
        return $this->csrf->token();
    }

    /**
     * Shortcut to all the class Fields methods
     * @param  string $fieldName
     * @param  array $args
     * @return Fields
     */
    public function __call(string $fieldName, array $args)
    {
        return call_user_func_array([$this->form, $fieldName], $args);
    }

    /*
    public function __call($a, $b)
    {
        if (method_exists($this->form, $a)) {
            return call_user_func_array([$this->form, $a], $b);
        } else {
            throw new BadMethodCallException("The method \"{$a}\" does not exist in the class \"" .
                get_class($this->form) . "\".", 1);
        }
    }
     */
}
