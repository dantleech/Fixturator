<?php

namespace DTL\Fixturator\Export;
use DTL\Fixturator\Fixture\Store;

class MySQLExporter
{
    protected $logger;
    protected $dbname;
    protected $connection;

    public function __construct($params, $logger)
    {
        $dsn = 'mysql:host='.$params['host'];
        $pdo = new \PDO($dsn, $params['user'], $params['pass']);
        $this->dbname = $params['dbname'];
        $this->logger = $logger;
        $this->connection = $pdo;
    }

    public function export(Store $store)
    {
        $this->logger->info('Nuking and recrating database "'.$this->dbname.'"');
        $this->connection->query('DROP '.$this->dbname);
        $this->connection->query('CREATE '.$this->dbname);
        $this->connection->query('USE '.$this->dbname);

        // hmmmm ....
    }
}
