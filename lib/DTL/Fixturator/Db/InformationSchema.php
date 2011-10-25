<?php

namespace DTL\Fixturator\Db;

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
        $sql = "SELECT tc.table_name, kcu.referenced_table_name, tc.constraint_type, kcu.referenced_column_name, kcu.column_name FROM information_schema.key_column_usage AS kcu LEFT JOIN information_schema.table_constraints AS tc ON kcu.table_schema = tc.table_schema AND kcu.table_name = tc.table_name AND kcu.constraint_name = tc.constraint_name WHERE kcu.table_schema = :dbname AND tc.table_name = :tableName AND tc.constraint_type = :type";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(
            ':dbname' => $this->getCurrentDatabaseName(),
            ':tableName' => $tableName,
            ':type' => $type,
        ));
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }

    public function getOneToManyRelationships($tableName)
    {
        $sql = "SELECT tc.table_name, kcu.referenced_table_name, tc.constraint_type, kcu.referenced_column_name, kcu.column_name FROM information_schema.key_column_usage AS kcu LEFT JOIN information_schema.table_constraints AS tc ON kcu.table_schema = tc.table_schema AND kcu.table_name = tc.table_name AND kcu.constraint_name = tc.constraint_name WHERE kcu.referenced_table_schema = :dbname AND kcu.referenced_table_name = :tableName AND tc.constraint_type = 'FOREIGN KEY'";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(
            ':dbname' => $this->getCurrentDatabaseName(),
            ':tableName' => $tableName,
        ));
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }

    public function getManyToOneConstraints($tableName)
    {
        return $this->getConstraintsByType($tableName, 'FOREIGN KEY');
    }

    public function getPkField($tableName)
    {
        // TODO: CACHE
        //
        $pks = $this->getConstraintsByType($tableName, 'PRIMARY KEY');

        if (count($pks) > 1) {
            throw new \Exception(sprintf('Tables with more than one primary key not currently supported on "%s".', $tableName));
        } elseif (count($pks) == 0) {
            throw new \Exception(sprintf('Table "%s" has no primary key.', $tableName));
        }

        return $pks[0]['column_name'];
    }
}
