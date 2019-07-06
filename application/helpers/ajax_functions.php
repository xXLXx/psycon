<?php

include "../config/config.php";

/* MAIN */

$link = mysql_connect($db['development']['hostname'], $db['development']['username'], $db['development']['password']) or die("Could not connect");
mysql_select_db($db['development']['database']) or die("Could not select database");


switch($_REQUEST['mode'])
{
	case 'check_username':
	checkUsername();
	break;
}

?>