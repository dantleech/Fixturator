<?php

namespace DTL\Fixturator\Map;

/**
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class PrimaryKey
{
    protected $pkFields = array();

    public function __construct()
    {
    }

    public function addField($pkField)
    {
        $this->pkFields[] = $pkField;
    }
}
