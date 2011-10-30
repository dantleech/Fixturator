<?php

namespace DTL\Fixturator\Map;

/**
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class Database
{
    protected $name;
    protected $tableIndex;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTable($tableName)
    {
        if (!isset($this->tableIndex[$tableName])) {
            $table = new Table($this, $tableName);
            $this->tableIndex[$tableName] = $table;
        } else {
            return $this->tableIndex[$tableName];
        }

        return $this->getTable($tableName);
    }

    public function dump()
    {
    }
}
