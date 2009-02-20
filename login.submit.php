<?php

include(dirname(__FILE__) . '/main.php');
$UserHandler = new UserHandler();
$UserHandler->getLogged($_POST['StEmail'],$_POST['StPassword']);
header('Location: index.php?page=listar');

?>

