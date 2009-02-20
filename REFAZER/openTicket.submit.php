<?php
include('main.php');
if (!headers_sent()) { session_start(); }
$TicketHandler = new TicketHandler();
$TicketHandler->createUserTicket(2,$_POST['categories'],$_POST['priorities'],$_POST['StTitle'],$_POST['StMessage'],$_POST['departments']);
?>