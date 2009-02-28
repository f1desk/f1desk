<?php

/* \ o / */
require_once(dirname(__FILE__) . '/main.php');
Validate::Session();

if (array_key_exists('page',$_GET)) {
  TemplateHandler::showPage($_GET['page']);
} else {
  TemplateHandler::showPage('listar');
}

?>