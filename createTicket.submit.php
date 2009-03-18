<?php
ob_start();
require_once('main.php');
require_once(TEMPLATEDIR . 'header.php');
if (!empty($_POST)) {
  $TicketHandler = new TicketHandler();
  foreach ($_POST as &$StArg) {
    UserHandler::SQLInjectionHandle($StArg);
  }

  $IDCategory = $_POST['StCategory'];
  $IDPriority = $_POST['StPriority'];
  $StTitle = $_POST['StTitle'];
  $TxMessage = f1desk_escape_html($_POST['TxMessage']);
  $IDDepartment = ($_POST['IDRecipient'] != 'null') ? $_POST['IDRecipient'] : '';
  $IDDepartmentReader = ($_POST['IDReader'] != 'null') ? $_POST['IDReader'] : '';
  $ArUsers = (isset($_POST['ArRecipients'])) ? explode(',',$_POST['ArRecipients']) : array();
  $ArReaders = (isset($_POST['ArReaders'])) ? explode(',',$_POST['ArReaders']) : array();

  if (TemplateHandler::IsSupporter()) {
    if (!empty($_FILES['Attachment']['name'])) {
      $IDTicket = $TicketHandler->createSupporterTicket(getSessionProp('IDSupporter'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment, $IDDepartmentReader,$ArUsers,$ArReaders,true,$_FILES);
    } else {
      $IDTicket = $TicketHandler->createSupporterTicket(getSessionProp('IDSupporter'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment, $IDDepartmentReader,$ArUsers,$ArReaders,true);
    }
  } else {
    if (!empty($_FILES['Attachment']['name'])) {
      $IDTicket = $TicketHandler->createUserTicket(getSessionProp('IDUser'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment,$_FILES);
    } else {
      $IDTicket = $TicketHandler->createUserTicket(getSessionProp('IDUser'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment);
    }
  }
  header('Location: index?page=escrever&IDTicket=' . $IDTicket);
}
require_once(TEMPLATEDIR . 'footer.php');
ob_end_flush();
?>