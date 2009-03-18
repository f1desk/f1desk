<?php

require_once('main.php');

/*default*/
handleLanguage(__FILE__);

$IDTicket = $_POST['IDTicket'];
$preview = (isset($_POST['preview']))?true:false;

$TicketHandler = new TicketHandler();
$TicketHandler->setAsRead(getSessionProp('IDUser'),$IDTicket);

$ArHeaders = TemplateHandler::getTicketHeaders($IDTicket);
$ArSupporters = TemplateHandler::listSupporters($IDTicket);
$ArMessages = TemplateHandler::getHistory($IDTicket);
$ArAttachments = TemplateHandler::getAttachments($IDTicket);

if (getSessionProp('isSupporter') == 'true') {
  $BoCreate = F1DeskUtils::getPermission('BoCreateCall',getSessionProp('IDSupporter'));
  if ($BoCreate) {
    $ArDepartments = TemplateHandler::getPublicDepartments(false);
  } else {
    $ArDepartments = TemplateHandler::getDepartments(getSessionProp('IDSupporter'));
  }
} else {
  $ArDepartments = TemplateHandler::getPublicDepartments();
}

$StTitle = $ArHeaders['StTitle'];
$IDTicket = $ArHeaders['IDTicket'];
$ArAttachedTickets = TemplateHandler::getAttachedTickets($IDTicket);
$ArTicketDepartments = TemplateHandler::getTicketDepartments($IDTicket);
$ArTicketDepartmentsReader = TemplateHandler::getTicketDepartmentsReader($IDTicket);
$ArDepartment = reset($ArTicketDepartments);
$ArTicketDestinations = TemplateHandler::getTicketDestination($IDTicket); // who this ticket was sent to
$ArTicketReaders = TemplateHandler::getTicketReaders($IDTicket);
$IDDepartment = $ArHeaders['IDDepartment'];
$StSituation = constant($ArHeaders['StSituation']);
$DtOpened = F1DeskUtils::formatDate('datetime_format',$ArHeaders['DtOpened']);

if (TemplateHandler::IsSupporter()) {
  $ArResponses = TemplateHandler::getCannedResponses(getSessionProp('IDSupporter'),$IDDepartment);
}

require_once(TEMPLATEDIR . '/ticket.php');
?>