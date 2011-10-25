<?php

namespace DTL\Fixturator\Fixture;
use DTL\Fixturator\Fixture\Table;

class Store
{
    protected $tableIndex = array();
    protected $tableStack = array();

    public function getTable($tableName)
    {
        if (isset($this->tableIndex[$tableName])) {
            return $this->tableIndex[$tableName];
        }

        $table = new Table($tableName);
        $this->tableIndex[$tableName] = $table;
        $this->tableStack[] = $table;

        return $this->tableIndex[$tableName];
    }
}
