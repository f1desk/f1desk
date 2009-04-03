<?php

  require_once('main.php');
  $IDSupporter = getSessionProp('IDSupporter');
  $IDUser = getSessionProp('IDUser');

  if (TemplateHandler::IsSupporter()) {
  	$ArDepartments = TemplateHandler::getDepartments( $IDSupporter, true );
  	$ArIDDepartments = array_keys($ArDepartments);
  	$ArTickets = TemplateHandler::getTickets($ArIDDepartments,$IDSupporter);
  } else {
    $ArDepartments = TemplateHandler::getUserDepartments();
    $ArIDDepartments = array_keys($ArDepartments);
    #
    # AINDA NUM FIZ ESSA FUNCAO !!!
    #
    $ArTickets = TemplateHandler::getUserTickets($ArIDDepartments,$IDUser);
  }

?>