<?php
  ob_start();
  require_once('../../loginData.php');
  header('Location: ../../index.php');
  ob_end_flush();
?>