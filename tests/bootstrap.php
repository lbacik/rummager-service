<?php

require_once '../config.php';
require __DIR__ . '/../vendor/autoload.php';

require_once '../src/rumsrv.php';

$dbname = 'test';
$pdo_sn = 'mysql:host=localhost;dbname='. $dbname .';charset=utf8';
$hostname = 'phpunit.test';
$moduleName = 'smtp';
$statusTODO = 'TODO';
$statusFINISHED = 'FINISHED';


