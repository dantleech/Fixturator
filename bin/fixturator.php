<?php

require_once(__DIR__.'/../bootstrap.php');

use DTL\Fixturator\Process as FixturatorProcess;
use DTL\Fixturator\Export\MySQLExporter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

ini_set('xdebug.max_nesting_level', -1);

$dsn = 'mysql:dbname=yprox;host=localhost';
$user = 'root';
$password = '';
$pdo = new \PDO($dsn, $user, $password);
$logger = new Logger('FixLog');
$logger->pushHandler(new StreamHandler(__DIR__.'/../log/fixturator.log'));

$exporter = new MySQLExporter(array(
    'host' => 'localhost', 
    'user' => 'root', 
    'dbname' => 'fucking_test',
    'pass' => ''
), $logger);
$process = new FixturatorProcess($pdo, $logger, $exporter, 'site', array(232), array('site' => array('_dataParentId' => true)));

$process->execute();
