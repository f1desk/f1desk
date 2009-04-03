<?php

/***************************************
 *           Create Submit             *
****************************************/
require_once('main.php');
if (!empty($_POST) && $_POST['StAction'] == 'create') {
  $TicketHandler = new TicketHandler();
  foreach ($_POST as &$StArg) {
    UserHandler::SQLInjectionHandle($StArg);
  }

  $IDCategory = $_POST['StCategory'];
  $IDPriority = $_POST['StPriority'];
  $StTitle = $_POST['StTitle'];
  $TxMessage = f1desk_escape_html($_POST['TxMessage']);
  $IDDepartment = ($_POST['IDRecipient'] != 'null') ? $_POST['IDRecipient'] : '';
  $IDDepartmentReader = (isset($_POST['IDReader']) && $_POST['IDReader'] != 'null') ? $_POST['IDReader'] : '';
  $ArUsers = (isset($_POST['ArRecipients'])) ? explode(',',$_POST['ArRecipients']) : array();
  $ArReaders = (isset($_POST['ArReaders'])) ? explode(',',$_POST['ArReaders']) : array();
  $ArAttached = (isset($_POST['ArAttached'])) ? explode(',',$_POST['ArAttached']) : array();

  if (F1DeskUtils::IsSupporter()) {
    if (!empty($_FILES['Attachment']['name'])) {
      $IDTicket = $TicketHandler->createSupporterTicket(getSessionProp('IDSupporter'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment, $IDDepartmentReader,$ArUsers,$ArReaders,true,$_FILES);
    } else {
      $IDTicket = $TicketHandler->createSupporterTicket(getSessionProp('IDSupporter'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment, $IDDepartmentReader,$ArUsers,$ArReaders,true);
    }
  } else {
    if (!empty($_FILES['Attachment']['name'])) {
      $IDTicket = $TicketHandler->createUserTicket(getSessionProp('IDClient'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment,$_FILES);
    } else {
      $IDTicket = $TicketHandler->createUserTicket(getSessionProp('IDClient'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment);
    }
  }
  if (!empty($ArAttached)) {
    foreach ($ArAttached as $IDAttach) {
      if (! F1DeskUtils::isAttached($IDTicket,$IDAttach))
      $TicketHandler->attachTicket($IDTicket,$IDAttach);
    }
  }
} elseif (!empty($_POST) && $_POST['StAction'] == 'addSupporters') {
  $ArSupporters = F1DeskUtils::getAllSupporters();
  if ($ArSupporters[0]['IDSupporter'] == 0) {
    array_shift($ArSupporters);
  }
}

/***************************************
 *           Create Data               *
****************************************/

if (getSessionProp('isSupporter') == 'true') {
  if (! isset($TicketHandler))
    $TicketHandler = new TicketHandler();
  else
    if(! is_a($TicketHandler,'TicketHandler'))
      $TicketHandler = new TicketHandler();
  $BoCreate = F1DeskUtils::getPermission('BoCreateCall',getSessionProp('IDSupporter'));
  if ($BoCreate) {
    $ArDepartments = $TicketHandler->getPublicDepartments(false);
  } else {
    $ArDepartments = F1DeskUtils::getDepartmentsFormatted(getSessionProp('IDSupporter'));
  }
} else {
  $ArDepartments = $TicketHandler->getPublicDepartments();
}

$ArPriorities = F1DeskUtils::listPriorities();
$ArCategories = F1DeskUtils::listCategories();
if (F1DeskUtils::IsSupporter()) {
  $ArSub = F1DeskUtils::getSubDepartments(getSessionProp('IDSupporter'));
}
?>