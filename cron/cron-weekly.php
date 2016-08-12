<?php
require_once('../config.php');
$db = new PDO($pdo_sn, $db_user, $db_pwd);
$sql = 'call smtp_garbage_collector';
$r = $db->prepare($sql);
$r->execute();

