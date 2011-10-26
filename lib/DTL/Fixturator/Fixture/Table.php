<?php

namespace DTL\Fixturator\Fixture;
use DTL\Fixturator\Db\PrimaryKey;

class Table
{
    protected $name;
    protected $rows = array();
    protected $store;

    public function __construct($store, $name)
    {
        $this->name = $name;
        $this->store = $store;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRowCount()
    {
        return count($this->rows);
    }

    public function addFixture($pk, $fixture)
    {
        if ($pk instanceOf PrimaryKey) {
            $this->rows[$pk->getId()] = $fixture;
        } else {
            $this->rows[$pk] = $fixture;
        }
    }

    public function hasFixture($pk)
    {
        if ($pk instanceOf PrimaryKey) {
            return isset($this->rows[$pk->getId()]);
        } else {
            return isset($this->rows[$pk]);
        }
    }
}
