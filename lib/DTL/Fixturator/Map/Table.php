<?php

namespace DTL\Fixturator\Map;
use DTL\Fixturator\Map\PrimaryKey;
use DTL\Fixturator\Map\Database;

/**
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class Table
{
    protected $primaryKey;
    protected $foreignKeys = array();
    protected $database;
    protected $name;

    public function __construct(Database $database, $name)
    {
        $this->database = $database;
        $this->name = $name;
    }

    public function setPrimaryKey(PrimaryKey $pk)
    {
        $this->primaryKey = $pk;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function addForeignKey(ForeignKey $foreignKey)
    {
        $this->foreignKeys[] = $foreignKey;
    }
}
