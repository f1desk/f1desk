<?php

  require_once('main.php');

  $ObjTicket = new TicketHandler();

  if (F1DeskUtils::IsSupporter()) {
    $IDSupporter = getSessionProp('IDSupporter');
  	$ArDepartments = F1DeskUtils::getDepartments($IDSupporter);
  	$ArIDDepartments = array_keys($ArDepartments);
  	$ArTickets = $ObjTicket->getTickets($ArIDDepartments,$IDSupporter);
  } else {
    $IDUser = getSessionProp('IDUser');
    $ArDepartments = F1DeskUtils::getUserDepartments();
    $ArIDDepartments = array_keys($ArDepartments);
    $ArTickets = $ObjTicket->getUserTickets($IDUser);
  }

?>