<?php

require_once(dirname(__FILE__) . '/main.php');

/*default*/
handleLanguage(__FILE__);

$IDTicket = $_POST['IDTicket'];

$TicketHandler = new TicketHandler();
$TicketHandler->setAsRead(getSessionProp('IDUser'),$IDTicket);

$ArHeaders = TemplateHandler::getTicketHeaders($IDTicket);
$ArSupporters = TemplateHandler::listSupporters($IDTicket);
$ArMessages = TemplateHandler::getHistory($IDTicket);

$StTitle = $ArHeaders['StTitle'];
$IDTicket = $ArHeaders['IDTicket'];
$IDDepartment = $ArHeaders['IDDepartment'];
$StSituation = constant($ArHeaders['StSituation']);
$DtOpened = F1DeskUtils::formatDate('datetime_format',$ArHeaders['DtOpened']);

$ArResponses = TemplateHandler::getCannedResponses($_SESSION['IDSupporter'],$IDDepartment);

require_once(TEMPLATEDIR . '/ticket.php');

?>