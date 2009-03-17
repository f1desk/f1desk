<?php
require_once('main.php');
handleLanguage(__FILE__);

foreach ($_POST as $Post) {
  UserHandler::SQLInjectionHandle($Post);
}

if(empty($_POST['StAction']))
  die(EXC_GLOBAL_EXPPARAM);

if (! empty($_POST['IDSupporter']))
  $IDSupporter = $_POST['IDSupporter'];

if(! empty($_POST['IDTicket']))
  $IDTicket = $_POST['IDTicket'];

if (! empty($_POST['IDDepartment']))
  $IDDepartment = $_POST['IDDepartment'];

$StAction = $_POST['StAction'];
$TicketHandler = new TicketHandler();

switch ($StAction) {
  case 'ignore':
    if (F1DeskUtils::isIgnored($IDSupporter,$IDTicket))
      die(ALREADY_IGNORED);
    if ($TicketHandler->ignoreTicket($IDSupporter,$IDTicket))
      die('ok');
    else
      die(ERROR_IGNORING);
  break;

  case 'unignore':
    if (! F1DeskUtils::isIgnored($IDSupporter,$IDTicket))
      die(ALREADY_UNIGNORED);
    $TicketHandler->deleteFromTable(DBPREFIX . 'Ignored', "IDSupporter = $IDSupporter AND IDTicket = $IDTicket",1);
    die('ok');
  break;

  case 'bookmark':
    if (F1DeskUtils::isBookmarked($IDSupporter,$IDTicket))
      die(ALREADY_BOOKMARKED);
    if ($TicketHandler->bookmarkTicket($IDSupporter,$IDTicket))
      die('ok');
    else
      die(ERROR_BOOKMARKING);
  break;

  case 'attach':
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
  break;

  case 'change':
    if (empty($_POST['IDTicket']) || empty($_POST['IDDepartment']))
      die(EXC_GLOBAL_EXPPARAM);
    else {
      $BoReturn = $TicketHandler->changeDepartment($IDTicket,$IDDepartment);
      if ($BoReturn)
        die('ok');
      else
        die(EXC_ERR_CHANGE);
    }
  break;
}
?>