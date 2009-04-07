<?php
require_once(dirname(__FILE__) . '/main.php');
$UserHandler = new UserHandler();
$UserHandler->getLogged($_POST['StEmail'],$_POST['StPassword']);
?>

