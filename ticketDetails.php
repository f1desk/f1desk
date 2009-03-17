<?php

require_once(dirname(__FILE__) . '/main.php');

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

$StTitle = $ArHeaders['StTitle'];
$IDTicket = $ArHeaders['IDTicket'];
$ArAttachedTickets = TemplateHandler::getAttachedTickets($IDTicket);
$ArTicketDepartments = TemplateHandler::getTicketDepartments($IDTicket);
$ArTicketDepartmentsReader = TemplateHandler::getTicketDepartmentsReader($IDTicket);
$ArTicketDestinations = TemplateHandler::getTicketDestination($IDTicket); // who this ticket was sent to
$ArTicketDestinationsReader = TemplateHandler::getTicketDestinationReader($IDTicket);
$IDDepartment = $ArHeaders['IDDepartment'];
$StSituation = constant($ArHeaders['StSituation']);
$DtOpened = F1DeskUtils::formatDate('datetime_format',$ArHeaders['DtOpened']);

if (TemplateHandler::IsSupporter()) {
  $ArResponses = TemplateHandler::getCannedResponses(getSessionProp('IDSupporter'),$IDDepartment);
}

require_once(TEMPLATEDIR . '/ticket.php');


?>