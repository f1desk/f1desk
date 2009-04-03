<?php

/* \ o / */
require_once(dirname(__FILE__) . '/main.php');
Validate::Session();

if (array_key_exists('page',$_GET)) {
  F1DeskUtils::showPage($_GET['page']);
} else {
  F1DeskUtils::showPage('listar');
}

?>