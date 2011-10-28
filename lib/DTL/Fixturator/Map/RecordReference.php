<?php

namespace DTL\Fixturator\Schema;

/**
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class RecordReference
{
    /** @var PrimaryKey */
    protected $pk;

    /** @var Table */
    protected $table;

    /** @var RelationshipCollection */
    protected $oneToManyRelations;

    /** @var RelationshipCollection */
    protected $manyToOneRelations;

}
