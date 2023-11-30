<?php

namespace Services;

use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Query\AbstractMigrate;

class Migration
{
    public $args;
    public $table;
    public $migName;
    public $mig;
    public $where;

    public function __construct(?RequestInterface $request = null)
    {
        if (!is_null($request)) {
            $this->args = $request->getCliArgs();
            $this->setMigration(ucfirst($this->args['table'] ?? ""));
        }
    }

    public function setMigration(string $table): void
    {
        $this->table = $table;
        $this->migName = $getMigClass = "Models\\Migrate\\{$this->table}";
        if (class_exists($getMigClass)) {
            $this->mig = new $getMigClass();
        }
    }

    public function getBuild(): AbstractMigrate
    {
        return $this->mig;
    }

    public function getTable(): string
    {
        return strtolower($this->table);
    }

    public function getName(): ?string
    {
        return $this->migName;
    }

    public function hasMigration(): bool
    {
        return (!is_null($this->mig));
    }

    public function hasColumn(string $check): bool
    {
        if (!$this->hasMigration()) {
            return false;
        }
        $whiteList = $this->mig->getBuild()->getColumns();
        return isset($whiteList[$check]);
    }

    public function hasColumns(array $check, &$missing = array()): bool
    {
        if (!$this->hasMigration()) {
            return false;
        }
        $whiteList = $this->mig->getBuild()->getColumns();

        $has = true;
        foreach ($check as $key => $_valNotUsed) {
            if (!isset($whiteList[$key])) {
                $missing[] = $key;
                $has = false;
            }
        }
        return $has;
    }

    public function setWhere($check)
    {
        if (is_array($check)) {
            $this->where = $check;
        }
        if (is_string($check)) {
            parse_str($check, $this->where);
        }

        if (!$this->hasColumns($this->where, $missingCol)) {
            throw new \Exception("The columns (" . implode(", ", $missingCol) . ") does not exists in the " .
                "database table \"" . $this->getTable() . "\"!", 1);
        }

        return $this->where;
    }

    public function validate()
    {

        if (!$this->hasMigration()) {
            throw new \Exception("The migration is missing. Make sure the \"table\" argument is right and migration " .
                "exists in Models/Migrate/{$this->table}!", 1);
        } elseif (is_null($this->where)) {
            throw new \Exception("The \"where\" argument is missing", 1);
        }
        return true;
    }
}
