<?php

require_once '../config.php';
require __DIR__ . '/../vendor/autoload.php';

require_once '../src/rumsrv.php';

$dbhost = 'rum-mysql';
$dbname = 'test';
$pdo_sn_0 = 'mysql:host=rum-mysql';
$pdo_sn = 'mysql:host=rum-mysql;dbname='. $dbname .';charset=utf8';
$hostname = 'phpunit.test';
$moduleName = 'smtp';
$statusTODO = 'TODO';
$statusFINISHED = 'FINISHED';


