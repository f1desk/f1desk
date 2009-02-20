<?php

require(dirname(__FILE__) . "/main.php");
$UserHandler = new UserHandler();
$UserHandler->logginOut();
header('Location: index.php');

?>