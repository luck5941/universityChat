<?php
require ('.errors.php');
require ('class.php');
$fun = $_POST['fun'];
switch ($fun) {
	case 'singin':
		$u = new User();
		echo $u->newUser($_POST['data']);
		break;
	case 'login':
		$u = new User();
		echo $u->login($_POST['data']['nick'], $_POST['data']['psswrd']);
		break;
	default:
		# code...
		break;
}
?>