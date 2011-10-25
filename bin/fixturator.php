<?php

require_once(__DIR__.'/../bootstrap.php');

use DTL\Fixturator\Process as FixturatorProcess;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$dsn = 'mysql:dbname=yprox;host=localhost';
$user = 'root';
$password = '';
$pdo = new \PDO($dsn, $user, $password);

$process = new FixturatorProcess($pdo, 'site', array(1, 2));

$logger = new Logger('FixLog');
$logger->pushHandler(new StreamHandler(__DIR__.'/../log/fixturator.log'));
$process->setLogger($logger);
$process->execute();
