<?php

$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");
$username = stripslashes($username);
$password = stripslashes($password);
$username = mysql_real_escape_string($username);
$password = mysql_real_escape_string($password);

require_once ('conf.php');
require_once ('database.php');

$conf = new Conf();
$path = new Path();
$msg = new Message();
$translate = new Translator();
$db = new Database($conf->db_host, $conf->db_name, $conf->db_user, $conf->db_password);

ob_start();
session_start();

$username_success = $db->check_username($username);
$password_success = $db->check_password($password);

if ($username_success && $password_success) {
	$_SESSION['username'] = $username;

	header("location: index.php");
}
else {
    sleep (1);
	header("location: index.php");
}

ob_end_flush();
?>

