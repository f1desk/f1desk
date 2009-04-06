<?php
  #
  # keeps the session (can only be called by Ajax request)
  #
  require_once(dirname(__FILE__) . '/main.php');
  Validate::Session();
  die('pong');
?>