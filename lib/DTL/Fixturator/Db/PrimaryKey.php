<?php

namespace DTL\Fixturator\Db;

class PrimaryKey
{
    protected $pks;

    public function addPk($field, $value)
    {
        $this->pks[$field] = $value;
    }

    public function getPkSql()
    {
        $fields = array();
        foreach ($pks as $field => $pk) {
            $sql[]= $field.' = '.$pk;
        }

        return implode(',', $sql);
    }

    public function getId()
    {
        return implode('-', array_values($this->pks));
    }
}
