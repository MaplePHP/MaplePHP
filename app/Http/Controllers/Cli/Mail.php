<?php

namespace Http\Controllers\Cli;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use Http\Controllers\Cli\CliInterface;
use Services\Stream\Cli as Stream;
use Services\ServiceMail;
use PHPMailer\PHPMailer\SMTP;

class Mail implements CliInterface
{
    public const REQUIRED = ["toMail", "toName", "subject", "body"];

    protected $container;
    protected $args;
    protected $cli;
    protected $mail;

    public function __construct(
        ContainerInterface $container,
        RequestInterface $request,
        Stream $cli,
        ServiceMail $mail
    ) {
        $this->container = $container;
        $this->args = $request->getCliArgs();
        $this->cli = $cli;
        $this->mail = $mail;
    }

    public function send(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        // If config has not been install, trigger missing package message
        if ($this->missingPackage()) {
            return $this->cli->getResponse();
        }

        $error = false;
        foreach (self::REQUIRED as $k) {
            if (!isset($this->args[$k])) {
                $error = true;
            }
        }

        if ($error) {
            $this->cli->write("\nERROR: The arguments (" . implode(", ", self::REQUIRED) . ") is required. " .
                "See example bellow!\n");
            $this->help();
        } else {
            try {
                $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

                if ($fromMail = ($this->args['fromMail'] ?? null)) {
                    $this->mail->setFrom($fromMail, ($this->args['fromName'] ?? getenv("MAIL_FROMNAME")));
                }
                $this->mail->addAddress($this->args['toMail'], $this->args['toName']);

                $this->mail->Subject = $this->args['subject'];
                $this->mail->Body    = $this->args['body'];
                $this->mail->AltBody = strip_tags($this->args['body']);

                $this->mail->send();
                $this->cli->write("Message has been sent");
            } catch (\Exception $e) {
                $this->cli->write("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            }
        }

        return $this->cli->getResponse();
    }

    public function help(): ResponseInterface
    {
        // If config has not been install, trigger missing package message
        if ($this->missingPackage()) {
            return $this->cli->getResponse();
        }

        $this->cli->write('$ mail [type] [--values, --values, ...]');
        $this->cli->write('Type: send, ...');
        $this->cli->write("Values: \n --fromMail=%s\n --fromName=%s\n --toMail=%s\n --toName=%s\n " .
            "--subject=%s\n --body=%s\n --help");
        $this->cli->write('Usage: $ php cli mail send --toMail=' . (string)getenv("MAIL_FROMEMAIL") . ' ' .
            '--toName=' . (string)getenv("MAIL_FROMNAME") . ' --subject="Test mail" --body="This is a test mail..."');
        return $this->cli->getResponse();
    }

    public function missingPackage(): bool
    {
        if (!getenv("MAIL_FROMEMAIL")) {
            $this->cli->write("Error:");
            $this->cli->write("The mail package is not installed, execute the command bellow:\n");
            $this->cli->write('$ php cli config install --type=mail');
            return true;
        }
        return false;
    }
}
