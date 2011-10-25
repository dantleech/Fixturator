<?php

namespace DTL\Fixturator;

use Ylly\CommandProcessor\Process as BaseProcess;
use Ylly\CommandProcessor\ProcessCommand as Command;
use DTL\Fixturator\Db\InformationSchema;
use DTL\Fixturator\Fixture\Store as FixtureStore;

class Process extends BaseProcess
{
    protected $tableName;
    protected $pks = array();
    protected $informationSchema;
    protected $connection;
    protected $fixtureStore;

    protected $fixtures = array();

    public function __construct(\PDO $connection, $tableName, $pks = array())
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->pks = $pks;

        $this->informationSchema = new InformationSchema($connection, $tableName);
        $this->fixtureStore = new FixtureStore;
    }

    public function configure()
    {
        $this->setTitle('Fixturator');
        $this->addCommand(new Command('doInit', 'Initialize tables'));
        $this->addCommand(new Command('doPullRecords', 'Pull the records'));
    }

    private function pullRecords($tableName, $sourceRecord)
    {
        // many to one records
        $manyToOneConstraints = $this->informationSchema->getManyToOneConstraints($tableName);
        foreach ($manyToOneConstraints as $manyToOneConstraint) {
            if (!array_key_exists($manyToOneConstraint['column_name'], $sourceRecord)) {
                throw new \Exception(sprintf('Column "%s" does not exist in table "%s". Columns: (%s)',
                    $manyToOneConstraint['column_name'],
                    $tableName,
                    implode(', ', array_keys($sourceRecord))
                ));
            }

            if ($pk = $sourceRecord[$manyToOneConstraint['column_name']]) {
                if ($this->fixtureStore->getTable($manyToOneConstraint['referenced_table_name'])->hasFixture($pk)) {
                    continue;
                }

                $this->getLogger()->info(' -- Pulling *-->1 record ['.$manyToOneConstraint['referenced_table_name'].' #'.$pk);

                $sql = sprintf("SELECT * FROM %s WHERE %s = :pk /** MANY-TO-ONE RECORDS */", 
                    $manyToOneConstraint['referenced_table_name'], 
                    $manyToOneConstraint['referenced_column_name']
                );

                $stmt = $this->connection->prepare($sql);
                $stmt->execute(array(':pk' => $pk));
                $res = $stmt->fetch(\PDO::FETCH_ASSOC);
                $this->fixtureStore->getTable($manyToOneConstraint['referenced_table_name'])->addFixture($pk, $res);
                $this->pullRecords($manyToOneConstraint['referenced_table_name'], $res);
            }
        }

        // one to many records
        $oneToManyRelationships = $this->informationSchema->getOneToManyRelationships($tableName);
        foreach ($oneToManyRelationships as $oneToManyRelationship) {
            $sql = sprintf("SELECT * FROM %s WHERE %s = :pk /** ONE-TO-MANY RECORDS */",
                $oneToManyRelationship['table_name'],
                $oneToManyRelationship['column_name']
            );

            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':pk' => $sourceRecord[$oneToManyRelationship['referenced_column_name']]));
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            $pkField = $this->informationSchema->getPkField($oneToManyRelationship['table_name']);

            if ($this->fixtureStore->getTable($oneToManyRelationship['table_name'])->hasFixture($res[$pkField])) {
                continue;
            }

            $this->getLogger()->info(' -- Pulling 1-->* record ['.$oneToManyRelationship['table_name'].' #'.$res[$pkField]);
        }
    }

    public function doInit(Command $command)
    {
        $pkField = $this->informationSchema->getPkField($this->tableName);
        $sourceRecords = array();

        // immediate records
        foreach ($this->pks as $pk) {
            $this->getLogger()->info(' -- Pulling source record ['.$this->tableName.'] #'.$pk);
            $sql = sprintf("SELECT * FROM %s WHERE %s = :pk /** SOURCE RECORDS */", $this->tableName, $pkField);
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(
                ':pk' => $pk,
            ));
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->fixtureStore->getTable($this->tableName)->addFixture($pk, $res);
            $sourceRecords[] = $res;
        }

        return $sourceRecords;
    }

    public function doPullRecords(Command $command)
    {
        $sourceRecords = $this->getCommand('doInit')->getResult();

        foreach ($sourceRecords as $sourceRecord) {
            $this->pullRecords($this->tableName, $sourceRecord);
        }
    }
}
