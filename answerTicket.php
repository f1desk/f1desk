<?php
require_once('main.php');
if (!empty($_FILES['Attachment']['name'])) {
  if (!empty($_POST)) {
    $TicketHandler = new TicketHandler();
    $IDWriter = ($_SESSION['isSupporter'] == true) ? $_SESSION['IDSupporter'] : $_SESSION['IDClient'];
    $ArMsgType = array('NORMAL' => '0', 'INTERNAL' => '1', 'SYSTEM' => '2');
    $MsgType = ($_POST['StMessageType'] != 'SATISFACTION') ? $ArMsgType[$_POST['StMessageType']] : 'SATISFACTION';
    $TicketHandler->answerTicket($IDWriter,$_POST['IDTicket'],$_POST['TxMessage'],$MsgType,$_FILES);
    print "<script>top.refreshCall({$_POST['IDTicket']});</script>";
  }
}
?>