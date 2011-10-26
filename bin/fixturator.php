<?php

require_once(__DIR__.'/../bootstrap.php');

use DTL\Fixturator\Process as FixturatorProcess;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

ini_set('xdebug.max_nesting_level', -1);

$dsn = 'mysql:dbname=yprox;host=localhost';
$user = 'root';
$password = '';
$pdo = new \PDO($dsn, $user, $password);
$logger = new Logger('FixLog');
$logger->pushHandler(new StreamHandler(__DIR__.'/../log/fixturator.log'));

$process = new FixturatorProcess($logger, $pdo, 'site', array(1));

$process->execute();
