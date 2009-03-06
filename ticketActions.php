<?php
if (empty($_POST['IDSupporter']) || empty($_POST['IDTicket']) ||  empty($_POST['StAction'])) {
  die('error');
}
require_once('main.php');
handleLanguage(__FILE__);
$IDSupporter = $_POST['IDSupporter'];
$IDTicket = $_POST['IDTicket'];
$StAction = $_POST['StAction'];
$TicketHandler = new TicketHandler();

if ($StAction == 'ignore') {
  if (F1DeskUtils::isIgnored($IDSupporter,$IDTicket))
    die(ALREADY_IGNORED);
  if ($TicketHandler->ignoreTicket($IDSupporter,$IDTicket))
    die('ok');
  else
    die(ERROR_IGNORING);
} elseif ($StAction == 'unignore') {
  if (! F1DeskUtils::isIgnored($IDSupporter,$IDTicket))
    die(ALREADY_UNIGNORED);
  $TicketHandler->deleteFromTable(DBPREFIX . 'Ignored', "IDSupporter = $IDSupporter AND IDTicket = $IDTicket",1);
  die('ok');
} elseif ($StAction == 'bookmark') {
  if (F1DeskUtils::isBookmarked($IDSupporter,$IDTicket))
    die(ALREADY_BOOKMARKED);
  if ($TicketHandler->bookmarkTicket($IDSupporter,$IDTicket))
    die('ok');
  else
    die(ERROR_BOOKMARKING);
}
?>