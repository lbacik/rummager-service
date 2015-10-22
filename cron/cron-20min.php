<?php

require_once('../config.php');
$db = new PDO($pdo_sn, $db_user, $db_pwd);
$sql = 'call node_status_update';
$r = $db->prepare($sql);
$r->execute();

