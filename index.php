<?php

require_once(dirname(__FILE__) . '/main.php');
Validate::Session();
$StPage = array_key_exists('page',$_GET) ? $_GET['page'] : '';
F1DeskUtils::showPage($StPage);

?>