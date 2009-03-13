<?php
require_once('main.php');
if (!empty($_POST)) {
  $TicketHandler = new TicketHandler();
  foreach ($_POST as &$StArg) {
    UserHandler::SQLInjectionHandle($StArg);
  }

  $IDCategory = $_POST['StCategory'];
  $IDPriority = $_POST['StPriority'];
  $StTitle = $_POST['StTitle'];
  $TxMessage = $_POST['TxMessage'];
  $IDDepartment = 6;//(isset($_POST['IDDepartment'])) ? $_POST['IDDepartment'] : '';
  $ArUsers = (isset($_POST['ArUsers'])) ? $_POST['ArUsers'] : array();
  $ArReaders = (isset($_POST['ArUsers'])) ? $_POST['ArUsers'] : array();

  if (TemplateHandler::IsSupporter()) {
    if (!empty($_FILES['Attachment']['name'])) {
      $TicketHandler->createSupporterTicket(getSessionProp('IDSupporter'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment,$ArUsers,$ArReaders,true,$_FILES);
    } else {
      $TicketHandler->createSupporterTicket(getSessionProp('IDSupporter'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment,$ArUsers,true);
    }
  } else {
    if (!empty($_FILES['Attachment']['name'])) {
      $TicketHandler->createUserTicket(getSessionProp('IDUser'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment,$_FILES);
    } else {
      $TicketHandler->createUserTicket(getSessionProp('IDUser'),$IDCategory,$IDPriority,$StTitle,$TxMessage,$IDDepartment);
    }
  }
  //die("<script>location.href = ?page=listar</script>");
}
?>