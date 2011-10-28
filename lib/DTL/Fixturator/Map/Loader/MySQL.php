<?php

namespace DTL\Fixturator\Map\Loader;

use DTL\Fixturator\Map\Table;
use DTL\Fixturator\Map\PrimaryKey;
use DTL\Fixturator\Map\ForeignKey;

/**
 * @author Daniel Leech <daniel@dantleech.com> 
 */
class MySQL
{
    protected $dbh;
    protected $logger;
    protected $tableIndex;

    protected $constraintData;
    protected $constraintDataInitialized = false;

    public function __construct(\PDO $dbh, $logger)
    {
        $this->dbh = $dbh;
        $this->logger = $logger;
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function mapDatabase($name)
    {
        $this->dbh->query('USE information_schema');
        $sql = "SELECT * FROM tables WHERE table_schema = :dbname AND table_type = 'BASE TABLE'";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute(array('dbname' => $name));
        $tableRows = $stmt->fetchAll();

        foreach ($tableRows as $tableRow) {
            if ($tableName = $tableRow['TABLE_NAME']) {
                $this->logger->info('Mapping table', array('tableName' => $tableName));
                $table = $this->getTable($name, $tableName);
                $this->initTable($table);
            }
        }
    }

    protected function getTable($dbname, $tableName)
    {
        $id = sprintf('%s-%s', $dbname, $tableName);

        if (!isset($this->tableNameIndex[$id])) {
            $table = new Table($dbname, $tableName);
            $this->tableNameIndex[$id] = $table;
        } else {
            return $this->tableNameIndex[$id];
        }

        return $this->getTable($dbname, $tableName);
    }

    protected function initTable(Table $table)
    {
        $constraintData = array();
        $sql = "SELECT tc.table_name, kcu.referenced_table_name, kcu.referenced_table_schema, tc.constraint_type, kcu.referenced_column_name, kcu.column_name FROM information_schema.key_column_usage AS kcu LEFT JOIN information_schema.table_constraints AS tc ON kcu.table_schema = tc.table_schema AND kcu.table_name = tc.table_name AND kcu.constraint_name = tc.constraint_name WHERE kcu.table_schema = :dbname AND tc.table_name = :tableName";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute(array(
            ':dbname' => $table->getDatabaseName(),
            ':tableName' => $table->getName(),
        ));
        $cons = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($cons as $con) {
            $constraintData[$con['constraint_type']][] = $con;
        }
        $this->mapPrimaryKey($table, $constraintData);
        $this->mapForeignKeys($table, $constraintData);
    }

    protected function mapPrimaryKey(Table $table, $constraintData)
    {
        if (!isset($constraintData['PRIMARY KEY'])) {
            throw new \Exception('Table '.$table->getName().' has no primary key(s)');
        }

        $pk = new PrimaryKey($table);
        foreach ($constraintData['PRIMARY KEY'] as $pkField) {
            $pk->addField($pkField['column_name']);
            $this->logger->info(' -- mapped PK', array('col' => $pkField['column_name']));
        }
    }

    protected function mapForeignKeys(Table $table, $constraintData)
    {
        if (!isset($constraintData['FOREIGN KEY'])) {
            return;
        }

        foreach ($constraintData['FOREIGN KEY'] as $foreignKey) {
            $referencedTable = $this->getTable($foreignKey['referenced_table_schema'], $foreignKey['referenced_table_name']);
            $table->addForeignKey(new ForeignKey(
                $foreignKey['column_name'],
                $referencedTable,
                $foreignKey['referenced_column_name']
            ));
            $this->logger->info(' -- mapped FK', array(
                'column_name' => $foreignKey['column_name'],
                'referenced_table_schema' => $foreignKey['referenced_table_schema'],
                'referenced_table_name' => $foreignKey['referenced_table_name'],
                'referenced_column_name' => $foreignKey['referenced_column_name'],
            ));
        }
    }
}
