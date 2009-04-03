<?php

#
# Load Language and configs
#
require_once('main.php');
handleLanguage(__FILE__);

/************************** ### Actions ### ***************************/

if (isset($_POST['StAction'])) {
  
	foreach ($_POST as $Post) {
    UserHandler::SQLInjectionHandle($Post);
  }

  $StAction = $_POST['StAction'];
  $TicketHandler = new TicketHandler();
  
  switch ($StAction) {
    case 'ignore':
      if (!is_numeric($_POST['IDSupporter']) || !is_numeric($_POST['IDTicket'])) {
      	$returnMessage = EXC_GLOBAL_EXPPARAM; $returnType = 'error';
      } else {
        if (F1DeskUtils::isIgnored($_POST['IDSupporter'], $_POST['IDTicket'])){
          $returnMessage = ALREADY_IGNORED; $returnType = 'error';
        } else {
          if (!$TicketHandler->ignoreTicket($_POST['IDSupporter'], $_POST['IDTicket'])){
            $returnMessage = ERROR_IGNORING; $returnType = 'error';
          } else {
            $BoIgnored = true;
            $returnMessage = SUCESS_IGNORED; $returnType = 'ok';
          }
        }
      }
    break;
  
    case 'unignore':
      if (!is_numeric($_POST['IDSupporter']) || !is_numeric($_POST['IDTicket'])) {
        $returnMessage = EXC_GLOBAL_EXPPARAM; $returnType = 'error';
      } else {
        if (!F1DeskUtils::isIgnored($_POST['IDSupporter'], $_POST['IDTicket'])){
          $returnMessage = ALREADY_UNIGNORED; $returnType = 'error';
        } else {
          $TicketHandler->deleteFromTable(DBPREFIX . 'Ignored', "IDSupporter = ".$_POST['IDSupporter']." AND IDTicket = ".$_POST['IDTicket'],1);
          $returnMessage = SUCESS_UNIGNORED; $returnType = 'ok';
          $BoIgnored = false;
        }
      }
    break;
  
    case 'bookmark':
      if (!is_numeric($_POST['IDSupporter']) || !is_numeric($_POST['IDTicket'])) {
        $returnMessage = EXC_GLOBAL_EXPPARAM; $returnType = 'error';
      } else {
        if (F1DeskUtils::isBookmarked($_POST['IDSupporter'], $_POST['IDTicket'])){
          $returnMessage = ALREADY_BOOKMARKED; $returnType = 'error';
        } else {
          if (!$TicketHandler->bookmarkTicket($_POST['IDSupporter'], $_POST['IDTicket'])){
            $returnMessage = ERROR_BOOKMARKING; $returnType = 'error';
          } else {
            $returnMessage = SUCESS_BOOKMARK; $returnType = 'ok';
          }
        }
      }
    break;
  
    case 'attach':
      if (!is_numeric($_POST['IDAttached'])){
        $returnMessage = EXC_GLOBAL_EXPPARAM; $returnType = 'error';
      } else {
        $IDAttached = $_POST['IDAttached'];
        if (F1DeskUtils::isAttached($IDTicket,$IDAttached)){
          $returnMessage = ALREADY_ATTACHED; $returnType = 'error';
        } else {
          $TicketHandler->attachTicket($IDTicket,$IDAttached);
          $returnMessage = SUCESS_ATTACHING; $returnType = 'ok';
        }
      }
    break;
  
    case 'changeDepartment':
      if (!is_numeric($_POST['IDTicket']) || !is_numeric($_POST['IDDepartment'])){
        $returnMessage = EXC_GLOBAL_EXPPARAM; $returnType = 'error';
      } else {
        $BoReturn = $TicketHandler->changeDepartment($_POST['IDTicket'], $_POST['IDDepartment']);
        if (!$BoReturn){
          $returnMessage = EXC_ERR_CHANGE; $returnType = 'error';
        } else {
          $returnMessage = SUCESS_CHANGEDEPARTMENT; $returnType = 'ok';
        }
      }
    break;
    
    case 'setOwner':
      if (!is_numeric($_POST['IDTicket']) || !is_numeric($_POST['IDSupporter'])){
        $returnMessage = EXC_GLOBAL_EXPPARAM; $returnType = 'error';
      } else {
        $Ticket = new TicketHandler();
        $Ticket->setTicketOwner($_POST['IDTicket'], $_POST['IDSupporter'], getSessionProp('IDUser'));
        $returnMessage = SUCESS_SETOWNER; $returnType = 'ok';
      }
    break;
  }
  
}

/************************** ### End - Actions ### ***************************/


/************************** ### Loading Data ### ***************************/

$IDTicket = $_POST['IDTicket'];
$IDSupporter = getSessionProp('IDSupporter');
$preview = (isset($_POST['preview']))?true:false;

$TicketHandler = new TicketHandler();
$TicketHandler->setAsRead(getSessionProp('IDUser'),$IDTicket);

$ArHeaders = TemplateHandler::getTicketHeaders($IDTicket);
$ArSupporters = TemplateHandler::listSupporters($IDTicket);
$ArAttachments = TemplateHandler::getAttachments($IDTicket);

if (getSessionProp('isSupporter') == 'true') {
  $BoCreate = F1DeskUtils::getPermission('BoCreateCall', $IDSupporter);
  if ($BoCreate) {
    $ArDepartments = TemplateHandler::getPublicDepartments(false);
  } else {
    $ArDepartments = TemplateHandler::getDepartments($IDSupporter);
  }
} else {
  $ArDepartments = TemplateHandler::getPublicDepartments();
}

#
# Ticket Header
#
$StTitle = $ArHeaders['StTitle'];
$IDTicket = $ArHeaders['IDTicket'];
$IDDepartment = $_POST['IDDepartment'];
//$IDDepartment = (!empty($ArHeaders['IDDepartment'])) ? $ArHeaders['IDDepartment'] : '';
$StSupporter = (!empty($ArHeaders['StName'])) ? $ArHeaders['StName'] : '';
$StSituation = $ArHeaders['StSituation'];
$BoIgnored = (isset($BoIgnored))?$BoIgnored:F1DeskUtils::isIgnored($IDSupporter, $IDTicket);

#
# Ticket Info
#
$ArAttachedTickets = TemplateHandler::getAttachedTickets($IDTicket);
$ArTicketsAttached = TemplateHandler::getTicketsAttached($IDTicket);
$ArTicketDepartments = TemplateHandler::getTicketDepartments($IDTicket);
$ArTicketDepartmentsReader = TemplateHandler::getTicketDepartmentsReader($IDTicket);
$ArTicketDestinations = TemplateHandler::getTicketDestination($IDTicket);
$ArTicketReaders = TemplateHandler::getTicketReaders($IDTicket);

$ArDepartment = reset($ArTicketDepartments);

$DtOpened = F1DeskUtils::formatDate('datetime_format',$ArHeaders['DtOpened']);
$StTicketCategory = TemplateHandler::getTicketCategory($IDTicket);
$StTicketPriority = TemplateHandler::getTicketPriority($IDTicket);
$StTicketType = TemplateHandler::getTicketType($IDTicket);

if (TemplateHandler::IsSupporter()) {
  $ArResponses = F1DeskUtils::listCannedResponses($IDSupporter,$IDDepartment);
}

/************************** ### End - Loading Data ### ***************************/
?>