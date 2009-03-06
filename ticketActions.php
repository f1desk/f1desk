<?php
require_once('main.php');
handleLanguage(__FILE__);

if(empty($_POST['StAction']))
  die(EXC_GLOBAL_EXPPARAM);

if (! empty($_POST['IDSupporter']))
  $IDSupporter = $_POST['IDSupporter'];

if(! empty($_POST['IDTicket']))
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
} elseif ($StAction == 'attach') {
  if (empty($_POST['IDAttached']))
    die(EXC_GLOBAL_EXPPARAM);
  else
    $IDAttached = $_POST['IDAttached'];
  if (F1DeskUtils::isAttached($IDTicket,$IDAttached))
    die(ALREADY_ATTACHED);
  else {
    $TicketHandler->attachTicket($IDTicket,$IDAttached);
    die('ok');
  }
}
?>