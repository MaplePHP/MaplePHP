<?php

namespace Http\Controllers\Private;

// Library
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use Http\Controllers\BaseController;
use Services\ServiceProvider;
use Services\Forms\LoginForm;
use Services\Users\LoginService;
use Services\ServiceMail;

class Login extends BaseController
{
    protected $form;
    protected $mail;
    protected $loginService;
    protected $attempt = 0;

    public function __construct(
        ServiceProvider $provider,
        LoginService $loginService,
        LoginForm $form,
        ServiceMail $mail
    ) {
        $this->form = $form;
        $this->mail = $mail;
        $this->loginService = $loginService;
    }

    public function form(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        $this->form->build();
        $url = $this->url()->select(["page"])->add(["model"])->getUrl();
        

        $this->view()->setPartial("form", [
            "name" => $this->local("auth")->get("signIn", "Sign in"),
            "content" => "You can use regular form like bellow or place form in a modal: " .
            "<a class=\"domer-get-btn\" href=\"#\" data-href=\"" . $url . "\">Click here</a>",
            "form" => [
                "method" => "post",
                "action" => $this->url()->reset()->add(["login"])->getUrl(),
                "form" => $this->form,
                "submit" => "Send"
            ]
        ]);

        return $response->clearCache();
    }

    public function formModel(): object
    {
        $this->form->build();

        $item = $this->responder()->setView("modal", [
            "type" => "opener",
            "headline" => $this->local("auth")->get("signIn", "Sign in"),
            "content" => "Lorem ipsum form responder"
        ]);

        $item->field($item->item("form"), [
            "data" => [
                "method" => "post",
                "action" => $this->url()->select(["page"])->getUrl(),
                "token" => $this->form->getToken(),
                "submit" => $this->local("auth")->get("signIn", "Sign in"),
            ],
            "fields" => $this->form->getFields()
        ]);

        return $this->responder()->build();
    }

    /**
     * Validate the login
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return object (json)
     */
    public function login(ResponseInterface $response, RequestInterface $request): object
    {
        $user = $this->loginService->validate($request);
        if ($user) {
            $this->responder()->redirect($this->url()->getRoot(\Http\Middlewares\LoggedIn::LOGIN_PATH));
        } else {
            $this->responder()->error($this->local("auth")->get("wrongCredentials", "Wrong credentials"));
        }

        $this->attempt++;
        return $this->responder()->build();
    }

    /**
     * Forgot password
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface
     */
    public function forgotPasswordForm(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        $this->view()->setPartial("form", [
            "name" => $this->local("auth")->get("resetPassword", "Reset password"),
            "content" => $this->local("auth")->get("resetPasswordInfo"),
            "form" => [
                "method" => "post",
                "action" => $this->url()->getUrl(),
                "form" => $this->form->inst()->text()->name("email")->attr([
                    "placeholder" => $this->local("auth")->get(["fillIn", "email"])
                ])->label($this->local("auth")->get("email", "Email"))->get(),
                "token" => $this->form->getToken(),
                "submit" => "Send"
            ]
        ]);

        return $response;
    }

    /**
     * Send forgotten password if received correct email address
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return object
     */
    public function forgotPasswordPost(ResponseInterface $response, RequestInterface $request): object
    {
        $param = $request->getParsedBody();
        if (!is_array($param)) {
            throw new \Exception("Parsed body is empty", 1);
        }
        $user = $this->loginService->generateForgetToken($param['email']);
        if (is_object($user)) {
            $changeURL = $this->url()->select("page")->add(["reset", $user->token])->getUrl();
            $view = $this->view()->withView("mail/text", [
                "section" => [
                    [
                        "content" => [
                            $this->view()->createTag("td", $this->local("auth")
                                ->get("resetPassword", "Reset password"), ["class" => "h4 title"]),
                            $this->view()->createTag("td", $this->local("auth")
                                ->get("hello", "Hello"), ["class" => "h1 title"]),
                            $this->view()->createTag("td", $this->local("auth")
                                ->get("resetPasswordInfo"), ["class" => "para-2"]),
                            $this->view()->createTag("td", $this->local("auth")
                                ->get("clickHere"), ["class" => "para-2"]),
                        ],
                        "button" => [
                            "url" => $changeURL,
                            "title" => $this->local("auth")->get("clickHere", "Click here"),
                            "legend" => $changeURL
                        ]
                    ],
                    [
                        "content" => "<em>" . $this->local("auth")->get("linkWillExpire", null, [
                            $this->loginService::FORGET_TOKEN_EXPIRE . " hours"
                        ]) . "</em>",
                    ]
                ],
                "footer" => [
                    "headline" => $this->local("auth")
                        ->get("anyQuestions", "Any questions?"),
                    "content" => $this->local("auth")
                        ->get("contactUsAt", "Contact us at") . ": " . (string)getenv("MAIL_FROMEMAIL")
                ]
            ]);

            // From adress is set in ENV but is overwritable!
            // $this->mail()->setFrom('from@example.com', 'Mailer');
            $this->mail->addAddress($user->email, "{$user->firstname} {$user->lastname}");
            $this->mail->Subject = $this->local("auth")->get("resetPassword", "Reset password");
            $this->mail->Body    = $view->get();
            $this->mail->AltBody = "We have received a request to reset the password associated with your account. " .
            "Click on the following link to proceed with the password change.\n{$changeURL}";
            $this->mail->send();
        }

        // Will redirect back to login page event if email do not exists. We do this out of security reasons.
        $message = $this->local("auth")->get("resetPasswordInfo");
        $this->responder()->okRedirect($message, $this->url()->select("page")->getUrl());

        return $this->responder()->build();
    }


    public function resetPasswordForm(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {

        $this->loginService->token()->setToken($this->url()->select(["token"])->get(), false);
        $userID = $this->loginService->token()->validate();
        if ($userID) {
            $this->view()->setPartial("form", [
                "name" => $this->local("auth")->get(["fillIn", "password"]),
                //"content" => "Fill in your email bellow and if you get an email...",
                "form" => [
                    "method" => "post",
                    "action" => $this->url()->getUrl(),
                    "form" => $this->form->inst()->text()->name("password")->attr([
                        "type" => "password", "placeholder" => $this->local("auth")->get(["fillIn", "password"])
                    ])->label($this->local("auth")->get("password", "Password"))->get(),
                    "token" => $this->form->getToken(),
                    "submit" => $this->local("auth")->get(["send", "Send"])
                ]
            ]);
        } else {
            $response = $response->withStatus(403, $this->local("auth")->get("tokenExpired", "Token expired"))
            ->setDescription($this->local("auth")->get("newPasswordRequest", "You need to request a new password."));
        }

        return $response;
    }

    public function resetPasswordPost(ResponseInterface $response, RequestInterface $request): object
    {
        $param = $request->getParsedBody();
        if (!is_array($param)) {
            throw new \Exception("Parsed body is empty", 1);
        }
        if (!$this->loginService->validatePassword($param['password'])) {
            $this->responder()->error($this->local("auth")->get("invalidPassword", "invalid password"));
        } else {
            $this->loginService->token()->setToken($this->url()->select(["token"])->get(), false);
            $userID = $this->loginService->token()->validate();
            if ($userID !== false && is_int($userID)) {
                $this->loginService->updatePassword($userID, $param['password']);
                $this->loginService->token()->disable($userID);

                $message = $this->local("auth")->get("youPasswordChanged", "Your password has changed");
                $this->responder()->okRedirect($message, $this->url()->select("page")->getUrl());
            } else {
                $this->responder()->error($this->local("auth")->get("tokenExpired", "Token expired") . ". " .
                    $this->local("auth")->get("newPasswordRequest", "You need to request a new password."));
            }
        }

        return $this->responder()->build();
    }
}
