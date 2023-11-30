<?php

// $ php cli config install --type=mail

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ServiceMail extends PHPMailer
{
    protected const ALLOWED_MAIL_ENCRYPTION = ["ssl", "tls"];

    protected $hasMail;
    protected $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
        parent::__construct($this->provider->env("APP_DEBUG")->toBool());

        // Defaults
        $this->hasMail = ($this->provider->env("MAIL_FROMEMAIL")->toBool() && $this->provider->env("MAIL_FROMNAME")->toBool());

        // phpMailer object (phpMailer do not seem to understand PSR)
        $this->CharSet = $this->provider->env("MAIL_FROMEMAIL", "UTF-8");

        if ($this->hasMail) {
            $this->setFrom($this->provider->env("MAIL_FROMEMAIL"), $this->provider->env("MAIL_FROMNAME"));
        }
        $this->isHTML(true);

        // If host is specified
        if (getenv("MAIL_HOST")) {
            $this->setupSMTP();
        }
    }

    /**
     * Setup default mail server
     * @return void
     */
    private function setupSMTP(): void
    {
        //$this->SMTPDebug = 0; // Enable verbose debug output
        $this->isSMTP(); // Set mailer to use SMTP
        // Specify main and backup SMTP servers ('smtp1.example.com;smtp2.example.com')
        $this->Host       = $this->provider->env("MAIL_HOST");
        $this->SMTPAuth   = true; // Enable SMTP authentication
        $this->Username   = $this->provider->env("MAIL_USERNAME"); // SMTP username
        $this->Password   = $this->provider->env("MAIL_PASSWORD"); // SMTP password
        $this->Port       = $this->provider->env("MAIL_PORT")->toInt(); // TCP port to connect to

        if ($enc = getenv("MAIL_ENCRYPTION")) {
            $enc = strtolower($enc);
            if (!in_array($enc, static::ALLOWED_MAIL_ENCRYPTION)) {
                throw new \Exception("Mail ENCRYPTION set in config ({$enc}) is not allowed, needs to be " .
                    "one of (" . implode(", ", static::ALLOWED_MAIL_ENCRYPTION) . ")!", 1);
            }
            $this->SMTPSecure = $enc; // Enable TLS/SSL encryption
        } else {
            // Disbale TLS and SSL
            $this->SMTPSecure = "";
            $this->SMTPAutoTLS = false;
        }
    }

    /**
     * Can be used to save mail sending meta
     * @return array
     */
    public function metaData(): array
    {
        return [
            "from" => [$this->From, $this->FromName],
            "sender" => $this->Sender,
            "subject" => $this->Subject,
            "body" => $this->Body,
            "altBody" => $this->AltBody,
            "to" => $this->to,
            "cc" => $this->cc,
            "bcc" => $this->bcc,
            "host" => $this->Host,
            "port" => $this->Port,
            "SMTPSecure" => $this->SMTPSecure
        ];
    }
}
