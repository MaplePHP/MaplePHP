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

        // User columns
        $userSet = [
            'firstname' => $this->cli->step("Firstname"),
            'lastname' => $this->cli->step("Lastname"),
            'oid' => $this->cli->step("Org. ID"),
            'phone' => $this->cli->step("Phone"),
            'role' => $this->cli->step("Role", 1),
            'create_date' => $this->cli->step("create_date", date("Y-m-d H:i:s", time()))
        ];
        // Login columns
        $email = $this->cli->step("Email");
        $password = $this->cli->step("Password");
        $status = $this->cli->step("Enabled?", 1);

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
            'org_name' => $this->cli->step("Org. name"),
            'org_create_date' => $this->cli->step("create_date", date("Y-m-d H:i:s", time()))
        ];
        $trans = DB::transaction();
        try {
            $insertID = $this->user->insertOrg($set)->insertID();
            $trans->commit();



            $this->cli->write("The user {$set['org_name']} ($insertID) has been created!");
        } catch (\Exception $e) {
            $trans->rollback();
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
