<?php
/**
 * This procedure should be called once per 24 hours - the curren (hourly)
 * frequence is caused by tests only...
 */
require_once('../config.php');
$db = new PDO($pdo_sn, $db_user, $db_pwd);
$sql = 'call etl_f_ip';
$r = $db->prepare($sql);
$r->execute();

