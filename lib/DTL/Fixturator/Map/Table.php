<?php

namespace DTL\Fixturator\Map;
use DTL\Fixturator\Map\PrimaryKey;

/**
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class Table
{
    protected $primaryKey;
    protected $foreignKeys = array();
    protected $dbname;
    protected $name;

    public function __construct($dbname, $name)
    {
        $this->dbname = $dbname;
        $this->name = $name;
    }

    public function setPrimaryKeys(PrimaryKey $pk)
    {
        $this->primaryKey = $pk;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDatabaseName()
    {
        return $this->dbname;
    }

    public function addForeignKey(ForeignKey $foreignKey)
    {
        $this->foreignKeys[] = $foreignKey;
    }
}
