<?php
$UserHandler = new UserHandler();
$UserHandler->logginOut();
header('Location: index.php');

?>