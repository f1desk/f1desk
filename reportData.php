<?php
  require_once('main.php');
  $TicketHandler = new TicketHandler();
  
  $ArTicketsByDepartment = $TicketHandler->reportTicketsByDepartment();
  $ArAnswersByDepartment = $TicketHandler->reportAnswersByDepartment();
  $ArAnswersBySupporter = $TicketHandler->reportAnswerBySupporter();
  $ArSupportersByDepartment = $TicketHandler->reportSupportersByDepartments();
?>