<?php

require_once '../config.php';
require_once '../classes/dbcommunication.php';
require_once '../classes/logging.php';
require_once '../classes/rumsrv.php';

$dbname = 'test';
$pdo_sn = 'mysql:host=localhost;dbname='. $dbname .';charset=utf8';
$hostname = 'phpunit.test';
$moduleName = 'smtp';
$statusTODO = 'TODO';
$statusFINISHED = 'FINISHED';


