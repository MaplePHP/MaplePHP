<?php

namespace Http\Controllers\Cli;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Http\Interfaces\StreamInterface;
use MaplePHP\Query\DB;
use Http\Controllers\Cli\CliInterface;
use Services\Stream\Cli as Stream;
use Models\Users;
use Services\Migration;
use MaplePHP\Roles\Role;

class Database extends Migrate implements CliInterface
{
    //protected $id;
    protected $user;

    public function __construct(
        ContainerInterface $container,
        RequestInterface $request,
        Stream $cli,
        Migration $mig,
        Users $user
    ) {
        parent::__construct($container, $request, $cli, $mig);
        $this->user = $user;
    }

    public function delete()
    {
        try {
            $this->migrate->setWhere(($this->args['where'] ?? null));
            if ($this->migrate->validate()) {
                $delete = DB::delete($this->migrate->getTable());
                foreach ($this->migrate->where as $col => $val) {
                    $delete->where($col, $val);
                }

                $this->cli->confirm(
                    "Are you sure you want to execute the command bellow?\n\n" . $delete->sql() . "\n",
                    function (StreamInterface $_stream) use ($delete) {
                        $delete->execute();
                        $this->cli->write("The Database row has been deleted!");
                    }
                );
            }

        } catch (\Exception $e) {
            $this->cli->write($e->getMessage());
        }
    }

    public function insertUser()
    {
        $orgs = $this->user->getAllOrgs("org_id,org_name", function($row) {
            return [$row->org_id => $row->org_name];
        });

        if(count($orgs) === 0) {
            $this->cli->write("You must first create a organisation");
            $this->cli->write("Enter the command:\n");
            $this->cli->write("php cli database insertOrg\n");
            return $this->cli->getResponse();
        }

        $orgID = $this->cli->chooseInput($orgs, "Choose organization");

        // User columns
        $userSet = [
            'firstname' => $this->cli->required("Firstname"),
            'lastname' => $this->cli->required("Lastname"),
            'oid' => $orgID,
            'phone' => $this->cli->required("Phone", "phone"),
            'role' => $this->cli->chooseInput(Role::getSupportedRoles(), "Choose role"),
            'create_date' => date("Y-m-d H:i:s", time())
        ];

        // Login columns
        $email = $this->cli->required("Email", "email");
        $password = $this->cli->maskedInput("Password", "lossyPassword");
        $status = 1;

        $trans = DB::transaction();
        try {
            $userID = $this->user->insertUser($userSet)->insertID();
            $loginID = $this->user->insertLogin($userID, $email, $password, $status)->insertID();
            $trans->commit();
            $this->cli->write("The user {$email} ({$userID}, {$loginID}) has been created!");
        } catch (\Exception $e) {
            $trans->rollback();
            $this->cli->write($e->getMessage());
        }

        return $this->cli->getResponse();
    }

    public function insertOrg()
    {
        $set = [
            'org_name' => $this->cli->required("Org. name"),
            'org_create_date' => date("Y-m-d H:i:s", time())
        ];

        try {
            $insertID = $this->user->insertOrg($set)->insertID();
            $this->cli->write("The organisation {$set['org_name']} ($insertID) has been created!\n");

            $this->cli->write("You can now add user to the organisation.");
            $this->cli->write("Enter the command:\n");
            $this->cli->write("php cli database insertUser\n");

        } catch (\Exception $e) {
            $this->cli->write($e->getMessage());
        }

        return $this->cli->getResponse();
    }

    public function help()
    {
        $this->cli->write('$ database [type] [--values, --values, ...]');
        $this->cli->write('Type: delete, insertOrg, insertUser');
        $this->cli->write('Values: --table=%s, --where=id=2#parent=0, --help');
        return $this->cli->getResponse();
    }
}
