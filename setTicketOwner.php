<?php

require_once(dirname(__FILE__) . '/main.php');

$IDSupporter = $_POST['IDSupporter'];
$IDTicket = $_POST['IDTicket'];

$Ticket = new TicketHandler();
$Ticket->setTicketOwner($IDTicket, $IDSupporter, getSessionProp('IDUser'));

die('done');
?>