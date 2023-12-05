<?php

namespace Models;

use MaplePHP\Security\Hash;
use MaplePHP\Validate\Inp;
use Models\Migrate\Users as UserDBStructure;
use Models\Migrate\Login as LoginDBStructure;
use Models\Migrate\Organizations as OrgDBStructure;
use Services\ServiceProvider;

class Users
{
    private $database;

    public function __construct(ServiceProvider $provider)
    {
        $this->database = $provider->DB();
    }

    /**
     * Makes select/insert/update of email consistent
     * @param  string $email
     * @return string
     */
    public static function formatEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * Get user by email
     * @param  string $email
     * @param  string $col
     * @return bool|object
     */
    public function getUserByEmail(string $email, string $col = "id,firstname,lastname,email"): bool|object
    {
        $select = $this->selectUser($col);
        $select->where("email", static::formatEmail($email));
        return $select->get();
    }

    /**
     * Get user by ID
     * @param  int    $identifier
     * @param  string $col
     * @return bool|object
     */
    public function getUserById(int $identifier, string $col = "id,firstname,lastname,email,oid,org_name"): bool|object
    {
        $select = $this->selectUser($col);
        $select->joinLeft(new OrgDBStructure());
        $select->whereId($identifier);
        return $select->get();
    }

    /**
     * Validate user login
     * @param  string $email
     * @param  string $password
     * @return bool|int (false or user id)
     */
    public function validateUserLogin(string $email, string $password): bool|int
    {
        $hash = new Hash($password);
        $obj = $this->getUserByEmail($email, "id,email,hashed_password");
        if (is_object($obj) && $hash->verify($obj->hashed_password)) {
            return (int)$obj->id;
        }
        return false;
    }

    /**
     * Get all users in org
     * @param  int    $oid [description]
     * @param  string $col [description]
     * @return array
     */
    public function getAllUsersInOrg(int $oid, string $col = "id,firstname,lastname,email,oid,org_name"): array
    {
        $select = $this->selectUser($col);
        $select->join(new OrgDBStructure());
        $select->whereOrg_id($oid);
        return $select->fetch();
    }

    /**
     * Get org
     * @param  int    $oid
     * @param  string $col
     * @return bool|object
     */
    public function getOrgById(int $oid, string $col = "org_id,org_name"): bool|object
    {
        $select = $this->database::select($col, new OrgDBStructure());
        $select->whereOrg_id($oid);
        return $select->get();
    }

    /**
    * Get all orgs
    * @param  string $col
    * @return array
    */
    public function getAllOrgs(string $col = "org_id,org_name", ?callable $callback = null): array
    {
        $select = $this->database::select($col, new OrgDBStructure());
        return $select->fetch($callback);
    }


    /**
     * Change password (will auto hash!) (can be called like a static method (shortcut))
     * @param  string $password Password (not hashed!)
     * @return string
     */
    protected function hashPassword(string $password): string
    {
        $hash = new Hash($password);
        return $hash->passwordHash();
    }

    /**
     * Insert user (can be called like a static method (shortcut))
     * @param  array  $set
     * @return mixed
     */
    public function insertUser(array $userSet): mixed
    {
        $insert = $this->database::insert(new UserDBStructure())->set($userSet);
        $insert->execute();
        return $insert;
    }

    /**
     * Insert login (can be called like a static method (shortcut))
     * @param  int      $userID
     * @param  string   $email
     * @param  string   $password
     * @param  int      $status
     * @return mixed
     */
    public function insertLogin(int $userID, string $email, string $password, int $status = 1): mixed
    {
        $email = static::formatEmail($email);
        if (!Inp::value($email)->email()) {
            throw new \Exception("The email is not a valid email address.", 1);
        }

        $insert = $this->database::insert(new LoginDBStructure())
        ->set([
            "user_id" => $userID, "email" => $email,
            "status" => $status, "hashed_password" => $this->hashPassword($password)
        ]);

        $insert->execute();
        return $insert;
    }

    /**
     * Insert user (can be called like a static method (shortcut))
     * @param  array  $set
     * @return mixed
     */
    public function insertOrg(array $set): mixed
    {
        $insert = $this->database::insert(new OrgDBStructure())->set($set);
        $insert->execute();
        return $insert;
    }

    /**
     * Change password (will auto hash!)  can be called like a static method (shortcut))
     * @param  string $password Password  Input a unhashed password, the method will hash it
     * @return mixed
     */
    public function updatePassword(int $userID, string $password): mixed
    {
        $update = $this->database::update(new LoginDBStructure());
        $update->set("hashed_password", $this->hashPassword($password));
        $update->where("user_id", $userID);
        $update->limit(1);
        return $update->execute();
    }

    /**
     * Select user with login join
     * @param  string $col
     * @return mixed
     */
    protected function selectUser(string $col): mixed
    {
        $select = $this->database::select($col, new UserDBStructure());
        $select->join(new LoginDBStructure());
        return $select;
    }
}
