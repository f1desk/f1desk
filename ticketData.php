<?php

#
# Load Language and configs
#
require_once('main.php');
handleLanguage(__FILE__);

$ObjTicket = new TicketHandler();
$ObjUser = new UserHandler();
$isSupporter = F1DeskUtils::isSupporter();

/************************** ### Actions ### ***************************/

if (isset($_POST['StAction'])) {

	foreach ($_POST as $Post) {
    UserHandler::SQLInjectionHandle($Post);
  }

  $StAction = $_POST['StAction'];

  switch ($StAction) {
    case 'ignore':
      if (!is_numeric($_POST['IDSupporter']) || !is_numeric($_POST['IDTicket'])) {
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        if (F1DeskUtils::isIgnored($_POST['IDSupporter'], $_POST['IDTicket'])){
          ErrorHandler::setNotice(ALREADY_IGNORED, 'error');
        } else {
          if (!$ObjTicket->ignoreTicket($_POST['IDSupporter'], $_POST['IDTicket'])){
            ErrorHandler::setNotice(ERROR_IGNORING, 'error');
          } else {
            $BoIgnored = true;
            ErrorHandler::setNotice(SUCESS_IGNORED, 'ok');
          }
        }
      }
    break;

    case 'unignore':
      if (!is_numeric($_POST['IDSupporter']) || !is_numeric($_POST['IDTicket'])) {
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        if (!F1DeskUtils::isIgnored($_POST['IDSupporter'], $_POST['IDTicket'])){
          ErrorHandler::setNotice(ALREADY_UNIGNORED, 'error');
        } else {
          $ObjTicket->deleteFromTable(DBPREFIX . 'Ignored', "IDSupporter = ".$_POST['IDSupporter']." AND IDTicket = ".$_POST['IDTicket'],1);
          ErrorHandler::setNotice(SUCESS_UNIGNORED, 'ok');
          $BoIgnored = false;
        }
      }
    break;

    case 'bookmark':
      if (!is_numeric($_POST['IDSupporter']) || !is_numeric($_POST['IDTicket'])) {
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        if (F1DeskUtils::isBookmarked($_POST['IDSupporter'], $_POST['IDTicket'])){
          ErrorHandler::setNotice(ALREADY_BOOKMARKED, 'error');
        } else {
          if (!$ObjTicket->bookmarkTicket($_POST['IDSupporter'], $_POST['IDTicket'])){
            ErrorHandler::setNotice(ERROR_BOOKMARKING, 'error');
          } else {
            ErrorHandler::setNotice(SUCESS_BOOKMARK, 'ok');
          }
        }
      }
    break;

    case 'attach':
      if (!is_numeric($_POST['IDAttached'])){
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        $IDAttached = $_POST['IDAttached'];
        if (F1DeskUtils::isAttached($_POST['IDTicket'],$IDAttached)){
          ErrorHandler::setNotice(ALREADY_ATTACHED, 'error');
        } else {
          $ObjTicket->attachTicket($_POST['IDTicket'],$IDAttached);
          ErrorHandler::setNotice(SUCESS_ATTACHING, 'ok');
        }
      }
    break;

    case 'changeDepartment':
      if (!is_numeric($_POST['IDTicket']) || !is_numeric($_POST['IDDepartment'])){
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        $BoReturn = $ObjTicket->changeDepartment($_POST['IDTicket'], $_POST['IDDepartment']);
        if (!$BoReturn){
          ErrorHandler::setNotice(EXC_ERR_CHANGE, 'error');
        } else {
          ErrorHandler::setNotice(SUCESS_CHANGEDEPARTMENT, 'ok');
        }
      }
    break;

    case 'setOwner':
      if (!is_numeric($_POST['IDTicket']) || !is_numeric($_POST['IDSupporter'])){
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        $Ticket = new TicketHandler();
        $Ticket->setTicketOwner($_POST['IDTicket'], $_POST['IDSupporter'], getSessionProp('IDUser'));
        ErrorHandler::setNotice(SUCESS_SETOWNER, 'ok');
      }
    break;

    case 'answer':
      if (empty($_POST['TxMessage']) || !is_numeric($_POST['IDTicket']) || empty($_POST['StMessageType'])){
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        $_POST['TxMessage'] = f1desk_escape_html($_POST['TxMessage']);
        $ObjTicket = new TicketHandler();
        $IDWriter = (getSessionProp('IDClient')) ? getSessionProp('IDClient') : getSessionProp('IDSupporter');
        $ArMessageType = array('NORMAL' => '0', 'INTERNAL' => '1', 'SYSTEM' => '2', 'SATISFACTION' => '3');
        if (!empty($_FILES['Attachment']['name'])) {
          $ObjTicket->answerTicket($IDWriter,$_POST['IDTicket'],$_POST['TxMessage'],$ArMessageType[$_POST['StMessageType']],$_FILES);
        } else {
          $ObjTicket->answerTicket($IDWriter,$_POST['IDTicket'],$_POST['TxMessage'],$ArMessageType[$_POST['StMessageType']]);
        }
        ErrorHandler::setNotice(SUCESS_ANSWERING, 'ok');
      }
    break;

    case 'previewAnswer':
      if (empty($_POST['TxMessage'])) {
        ErrorHandler::setNotice(EXC_GLOBAL_EXPPARAM, 'error');
      } else {
        $TxMessagePreview = $ObjTicket->getPreviewAnswer(getSessionProp('IDUser'), $_POST['TxMessage'], $isSupporter);
      }
    break;
  }

}

/************************** ### End - Actions ### ***************************/


/************************** ### Loading Data ### ***************************/

$IDTicket = $_POST['IDTicket'];
$IDSupporter = getSessionProp('IDSupporter');
$preview = (isset($_POST['preview']))?true:false;

$ObjTicket = new TicketHandler();
$ObjTicket->setAsRead(getSessionProp('IDUser'),$IDTicket);
$ArHeaders = $ObjTicket->getTicketHeaders($IDTicket);
$ArAttachments = $ObjTicket->getAttachments($IDTicket);

if ($isSupporter) {
  $ArSupporters = $ObjUser->listSupporters($IDTicket);
  $BoCreate = F1DeskUtils::getPermission('BoCreateCall', $IDSupporter);

  if ($BoCreate) {
    $ArDepartments = F1DeskUtils::getPublicDepartments(false);
  } else {
    $ArDepartments = F1DeskUtils::getDepartmentsFormatted($IDSupporter);
  }

} else {
  /**
   * NAUM EH UTILIZADO QUANDO PARA CLIENTES !!!
   */
  $ArDepartments = F1DeskUtils::getPublicDepartments();
}

#
# Ticket Header
#
$StTitle = $ArHeaders['StTitle'];
$IDTicket = $ArHeaders['IDTicket'];
$IDDepartment = $_POST['IDDepartment'];
$StSupporter = (!empty($ArHeaders['StName'])) ? $ArHeaders['StName'] : '';
$StSituation = $ArHeaders['StSituation'];
if ($isSupporter) {
  $BoIgnored = (isset($BoIgnored)) ? $BoIgnored : F1DeskUtils::isIgnored($IDSupporter, $IDTicket);
}

#
# Ticket Info
#
if ($isSupporter) {
  $ArAttachedTickets = $ObjTicket->getAttachedTickets($IDTicket);
  $ArTicketsAttached = $ObjTicket->getTicketsAttached($IDTicket);
  $ArTicketDepartments = $ObjTicket->getTicketDepartments($IDTicket);
  $ArTicketDepartmentsReader = $ObjTicket->getTicketDepartmentsReader($IDTicket);
  $ArTicketDestinations = $ObjTicket->getTicketDestination($IDTicket);
  $ArTicketReaders = $ObjTicket->getTicketReaders($IDTicket);
}

$ArMessages = $ObjTicket->listTicketMessages($IDTicket);
$DtOpened = F1DeskUtils::formatDate('datetime_format',$ArHeaders['DtOpened']);
$StTicketCategory = $ObjTicket->getTicketCategory($IDTicket);
$StTicketPriority = $ObjTicket->getTicketPriority($IDTicket);
$StTicketType = $ObjTicket->getTicketType($IDTicket);

if ($isSupporter) {
  $ArResponses = F1DeskUtils::listCannedResponses($IDSupporter,$IDDepartment);
}

/************************** ### End - Loading Data ### ***************************/
?>