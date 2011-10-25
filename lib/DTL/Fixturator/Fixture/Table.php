<?php

namespace DTL\Fixturator\Fixture;

class Table
{
    protected $name;
    protected $rows = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function addFixture($pk, $fixture)
    {
        $this->rows[$pk] = $fixture;
    }

    public function hasFixture($pk)
    {
        return isset($this->rows[$pk]);
    }
}
