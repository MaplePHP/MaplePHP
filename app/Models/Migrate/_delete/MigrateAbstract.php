<?php

namespace Models\Migrate;

use MaplePHP\Query\Create;

abstract class AbstractMigrate
{
    protected $mig;

    abstract protected function buildTable();

    public function __construct(string $table, ?string $prefix = null)
    {
        if (is_null($prefix)) {
            $prefix = getenv("MYSQL_PREFIX");
        }
        $this->mig = new Create($table, ($prefix !== false) ? $prefix : null);
        $this->mig->auto();
    }

    public function getBuild(): Create
    {
        $this->buildTable();
        return $this->mig;
    }

    /**
     * Will drop table when method execute is triggered
     * @return array
     */
    public function drop(): array
    {
        $this->mig->drop();
        return $this->mig->execute();
    }

    /**
     * Read migration changes (before executing)
     * @return string
     */
    public function read(): string
    {
        $this->buildTable();
        if (!$this->mig->getColumns()) {
            throw new \Exception("There is nothing to read in migration.", 1);
        }
        return $this->mig->build();
    }

    /**
     * Will create/alter all table
     * @return string
     */
    public function create()
    {
        $this->buildTable();
        if (!$this->mig->getColumns()) {
            throw new \Exception("There is nothing to commit in migration.", 1);
        }
        return $this->mig->execute();
    }

    public function getMessage(array $error, string $success = "Success!")
    {
        if (count($error) > 0) {
            $sqlMessage = "";
            foreach ($error as $key => $val) {
                $sqlMessage .= "{$key}: {$val}\n";
            }
        } else {
            $sqlMessage = $success;
        }
        return $sqlMessage;
    }
}
