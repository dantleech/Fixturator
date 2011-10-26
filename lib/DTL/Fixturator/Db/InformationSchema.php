<?php

namespace DTL\Fixturator\Db;
use DTL\Fixturator\Db\PrimaryKey;

class InformationSchema
{
    protected $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getCurrentDatabaseName()
    {
        $sql = "SELECT DATABASE() as name";
        $res = $this->connection->query($sql)->fetch(\PDO::FETCH_ASSOC);
        return $res['name'];
    }

    public function getConstraintsByType($tableName, $type)
    {
        static $cache = array();

        if (isset($cache[$tableName][$type])) {
            return $cache[$tableName][$type];
        }

        $sql = "SELECT tc.table_name, kcu.referenced_table_name, tc.constraint_type, kcu.referenced_column_name, kcu.column_name FROM information_schema.key_column_usage AS kcu LEFT JOIN information_schema.table_constraints AS tc ON kcu.table_schema = tc.table_schema AND kcu.table_name = tc.table_name AND kcu.constraint_name = tc.constraint_name WHERE kcu.table_schema = :dbname AND tc.table_name = :tableName AND tc.constraint_type = :type";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(
            ':dbname' => $this->getCurrentDatabaseName(),
            ':tableName' => $tableName,
            ':type' => $type,
        ));
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $cache[$tableName][$type] = $res;

        return $this->getConstraintsByType($tableName, $type);
    }

    public function getOneToManyRelationships($tableName)
    {
        static $cache = array();

        if (isset($cache[$tableName])) {
            return $cache[$tableName];
        }

        $sql = "SELECT tc.table_name, kcu.referenced_table_name, tc.constraint_type, kcu.referenced_column_name, kcu.column_name FROM information_schema.key_column_usage AS kcu LEFT JOIN information_schema.table_constraints AS tc ON kcu.table_schema = tc.table_schema AND kcu.table_name = tc.table_name AND kcu.constraint_name = tc.constraint_name WHERE kcu.referenced_table_schema = :dbname AND kcu.referenced_table_name = :tableName AND tc.constraint_type = 'FOREIGN KEY'";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(
            ':dbname' => $this->getCurrentDatabaseName(),
            ':tableName' => $tableName,
        ));
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $cache[$tableName] = $res;

        return $this->getOneToManyRelationships($tableName);
    }

    public function getManyToOneConstraints($tableName)
    {
        return $this->getConstraintsByType($tableName, 'FOREIGN KEY');
    }

    public function getPkFields($tableName)
    {
        $pkFields = array();
        $pks = $this->getConstraintsByType($tableName, 'PRIMARY KEY');

        if (count($pks) == 0) {
            throw new \Exception(sprintf('Table "%s" has no primary key.', $tableName));
        }

        foreach ($pks as $pkField) {
            $pkFields[] = $pkField['column_name'];
        }

        return $pkFields;
    }

    public function getPk($tableName, $recordOrInteger)
    {
        $pkFields = $this->getPkFields($tableName);
        $pk = new PrimaryKey;

        if (is_integer($recordOrInteger)) {
            $pk->addPk(current($pkFields), $recordOrInteger);
        } else {
            foreach ($pkFields as $pkField) {
                if (!array_key_exists($pkField, $recordOrInteger)) {
                    var_dump($recordOrInteger);
                    echo $tableName;
                }
                $pk->addPk($pkField, $recordOrInteger[$pkField]);
            }
        }

        return $pk;
    }
}
