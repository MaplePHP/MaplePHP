<?php

namespace Services\Users;

use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Security\Hash;
use MaplePHP\Validate\Inp;
use MaplePHP\Security\Token;
use MaplePHP\DTO\Traverse;
use Models\Users;
use Services\ServiceProvider;
use Services\Forms\Validate;
use Services\Forms\LoginForm;
use InvalidArgumentException;

class LoginService
{
    //lossy: Will only validate if input edcharacter is allowed
    //strict: (at least one: lowercase, uppercase, digit and special character)
    public const PASS_SECURITY = "strict"; // (lossy or strict)
    public const MIN_PASS_LENGTH = 8;
    public const FORGET_TOKEN_EXPIRE = 24; // HOURS!
    public const ALLOW_REMEMBER_ME_TOKEN = true; // HOURS!
    public const TROTTLE_ATTEMPTS = true;

    private $provider;
    private $token;
    private $validate;
    private $form;
    private $users;
    private $attempt;


    public function __construct(ServiceProvider $provider, Users $users, Validate $validate, LoginForm $form)
    {
        $this->provider = $provider;
        $this->users = $users;
        $this->validate = $validate;
        $this->form = $form;

        if (!$this->provider->has("session")) {
            throw new \Exception("You need to include the SessionStart middleware!", 1);
        }
        $this->attempt = (int)$this->provider->session()->get("attempt");
    }

    /**
     * Validate DB user password with request password
     * @return bool
     */
    public function validate(RequestInterface $request): bool|object
    {
        // Will throttle if multiple login attempts fails
        if (static::TROTTLE_ATTEMPTS) {
            $this->attemptThrottle();
        }

        if ($requests = $this->validate->validate($this->form, $request->getParsedBody())) {
            $email = Users::formatEmail($requests['email']);
            if ($userID = $this->users->validateUserLogin($email, $requests['password'])) {
                // Geneate remember me token
                if (static::ALLOW_REMEMBER_ME_TOKEN && (int)($requests['remember'] ?? 0) === 1) {
                    $this->rememberMe($userID);
                }
                $this->provider->session()->setLogin($userID);
                $this->provider->logger()->notice("Login succes: {email} (user id: {id})", [
                    "id" => $userID,
                    "email" => $email
                ]);

                $this->provider->session()->delete("attempt");
                return $userID;
            } else {
                $this->provider->logger()->notice("Login error: Wrong credentials ({email})", $requests);
            }
        }

        // Add failed attempts
        $this->attempt++;
        $this->provider->session()->add("attempt", $this->attempt);

        return false;
    }

    /**
     * Generate a forget password token
     * @return bool|Traverse
     */
    public function generateForgetToken(string $email): bool|Traverse
    {
        if ($userRow = $this->users->getUserByEmail($email, "id,email,firstname,lastname")) {
            $this->token()->setExpires(3600 * self::FORGET_TOKEN_EXPIRE);
            if ($this->token()->generate($userRow->id, false)) {
                $token = $this->token()->getToken();
                $obj = Traverse::value($userRow);
                $obj->token = $token;
                return $obj;
            }
        }
        return false;
    }

    /**
     * Validate if password is strong enough
     * @param  string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        if (Inp::value($password)->length(self::MIN_PASS_LENGTH, 60)) {
            if (self::PASS_SECURITY === "strict") {
                return Inp::value($password)->strictPassword();
            }
            return Inp::value($password)->lossyPassword();
        }
        return false;
    }

    /**
     * Update the password
     * @param  int    $userID
     * @param  string $password New password "Not" hashed
     * @return bool
     */
    public function updatePassword(int $userID, string $password): bool
    {
        if (!$this->validatePassword($password)) {
            throw new \Exception("The password is not in a valid format or length is not " .
                "(" . self::MIN_PASS_LENGTH . "-60)!", 1);
        }
        return $this->users->updatePassword($userID, $password);
    }

    /**
     * Generate token
     * @param  int    $userID
     * @return void
     */
    public function rememberMe(int $userID): void
    {
        $this->token()->generate($userID);
    }

    /**
     * Make reuqest slower if multiple login attempts has failed
     * @return void
     */
    protected function attemptThrottle(): void
    {
        if ($this->attempt > 30) {
            sleep(4);
        } elseif ($this->attempt > 20) {
            sleep(2);
        } elseif ($this->attempt > 8) {
            sleep(1);
        }
    }

    /**
     * Get Token instance
     * @return Token
     */
    public function token(): Token
    {
        if (is_null($this->token)) {
            $this->token = new Token(Token::REMEMBER, $this->provider->cookies()->inst(), $this->provider->DB(), $this->provider->date());
        }
        return $this->token;
    }
}
