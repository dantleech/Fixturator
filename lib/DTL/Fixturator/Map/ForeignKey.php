<?php

namespace DTL\Fixturator\Map;
use DTL\Fixturator\Map\Table;

/**
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class ForeignKey
{
    protected $columnName;
    protected $referencedTable;
    protected $referencedColumnName;

    public function __construct($columnName, Table $referencedTable, $referencedColumnName)
    {
        $this->columnName = $columnName;
        $this->referencedTable = $referencedTable;
        $this->referencedColumnName = $referencedColumnName;
    }
}
