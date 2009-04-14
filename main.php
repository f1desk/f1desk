<?php

session_start();
header("Content-Type: text/html; charset=UTF-8",true);
require_once(dirname(__FILE__) . '/includes/settings.php');
require_once(dirname(__FILE__) . '/includes/config.php');
require_once(LANGDIR . 'languages.php');
require_once(INCLUDESDIR . 'dbConfig.php');
require_once(INCLUDESDIR . 'autoLoad.php');
require_once(CLASSDIR . 'ErrorHandler.php');

?>