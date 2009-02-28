<?php
  #
  # handles the session
  #
  require_once(dirname(__FILE__) . '/main.php');
  Validate::Session();
  die('pong');
?>