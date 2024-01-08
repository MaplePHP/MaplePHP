<?php

namespace Http\Controllers\Examples;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use Http\Controllers\BaseController;
use MaplePHP\Foundation\Http\Provider;
use MaplePHP\Foundation\Form\Validate;
use MaplePHP\Foundation\Form\Forms\ContactForm;

class ExampleForm extends BaseController
{
    protected $form;
    protected $validate;

    public function __construct(Provider $provider, ContactForm $form, Validate $validate)
    {
        $this->form = $form;
        $this->validate = $validate;
    }

    /**
     * The Contact page (see router)
     * @param  ResponseInterface $response PSR-7 Response
     * @param  RequestInterface  $request  PSR-7 Request
     * @return void
     */
    public function contactFrom(ResponseInterface $response, RequestInterface $request)
    {
        $this->form->build();
        $url = $this->url()->withType(["page"])->add(["modal"])->getUrl();
        $this->view()->setPartial("form", [
            "tagline" => getenv("APP_NAME"),
            "name" => "Contact us",
            "content" => "You can use regular form like bellow or place form in a modal: " .
            "<a class=\"maple-get-btn\" href=\"#\" data-href=\"" . $url . "\">Click here</a>",
            "form" => [
                "method" => "post",
                "action" => $this->url()->getUrl(),
                "form" => $this->form,
                "submit" => "Send"
            ]
        ]);
    }

    /**
     * Same as above BUT the form is loaded dynamically with Stratox.js instead of statically
     * @param  ResponseInterface $response PSR-7 Response
     * @param  RequestInterface  $request  PSR-7 Request
     * @return ResponseInterface
     */
    public function contactDynamicForm(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        $this->form->build();
        $item = $this->responder()->setView(["form" => "#formView"], [
            "method" => "post",
            "action" => $this->url()->select(["page"])->getUrl(),
            "token" => $this->form->getToken(),
            "submit" => "Send",
        ]);

        $item->form($this->form->getFields());
        return $response;
    }

    /**
     * Will dynamically open a modal/popup and add a dynamic form inside it with Stratox.js
     * @return object json data
     */
    public function contactFormModal(): object
    {
        $this->form->build();
        $item = $this->responder()->setView("modal", [
            "type" => "opener",
        ]);

        $item->item("ingress", [
            "headline" => "Contact us",
            "content" => "Lorem ipsum dolor"
        ]);

        //$item->form($this->form->getFields());
        $item->field($item->item("form"), [
            "data" => [
                "method" => "post",
                "action" => $this->url()->select(["page"])->getUrl(),
                "token" => $this->form->getToken(),
                "submit" => "Send",
            ],
            "fields" => $this->form->getFields()
        ]);

        // Responder will pass response to frontend and Stratox.js
        return $this->responder()->build();
    }

    /**
     * POST: Validate the contact form (see router)
     * @param  ResponseInterface $response PSR-7 Response
     * @param  RequestInterface  $request  PSR-7 Request
     * @return object json data
     */
    public function post(ResponseInterface $response, RequestInterface $request): object
    {
        if ($requests = $this->validate->validate($this->form, $request->getParsedBody())) {
            // The $requests variable will contain all "expected" form fields post values
            $this->responder()->message("Completed!");
        }
        // Responder will pass response to frontend and Stratox.js
        return $this->responder()->build();
    }
}
