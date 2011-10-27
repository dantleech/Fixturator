<?php

namespace DTL\Fixturator\Fixture;
use DTL\Fixturator\Fixture\Table;

class Store
{
    protected $tableIndex = array();
    protected $tableStack = array();
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function getTable($tableName)
    {
        if (isset($this->tableIndex[$tableName])) {
            return $this->tableIndex[$tableName];
        }

        $this->logger->info('Creating new table '.$tableName);
        $table = new Table($this, $tableName);
        $this->tableIndex[$tableName] = $table;
        $this->tableStack[] = $table;
        $this->logger->info(count($this->tableIndex).' tables in stack');

        return $this->tableIndex[$tableName];
    }

    public function report()
    {
        $report = array();

        foreach ($this->tableStack as $i => $table) {
            $report[] = $i.'. '.$table->getName().' '.$table->getRowCount().' rows.';
        }

        return implode("\n", $report);
    }

    public function getTableStack()
    {
        return $this->tableStack;
    }
}
