<?php

#
#
# Precisa ser refeita, pois não necessariamente o login sera dessa forma !!!
# a pagina de resposta que deve incluir a login.submit.php, que virara loginData.php
#
#
include(dirname(__FILE__) . '/main.php');
$UserHandler = new UserHandler();
$UserHandler->getLogged($_POST['StEmail'],$_POST['StPassword']);
header('Location: index.php?page=listar');

?>

