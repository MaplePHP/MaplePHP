<?php

namespace Services\Forms;

use Models\Form;
use Services\ServiceProvider;

abstract class AbstractForm
{
    protected $form;
    protected $provider;

    public function __construct(ServiceProvider $provider, Form $form)
    {
        $this->form = $form;
        $this->provider = $provider;
        $this->createForm();
    }

    /**
     * Create form - Setup the form inside of this method
     * @return void
     */
    abstract protected function createForm(): void;

    /**
     * Direct access form instance
     * @return form
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * Shortcut to all the class Models\Form\Form AND MaplePHP\Form\Fields methods!
     * If thrown Error, then it will be triggered from Models\Form\Form
     * @param  string $a Method name
     * @param  array $b argumnets
     * @return mixed
     */
    public function __call($a, $b)
    {
        return call_user_func_array([$this->form, $a], $b);
    }
}
