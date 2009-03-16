<?php
require_once('main.php');
if (!empty($_POST)) {
  switch ($_POST['action']) {
    case 'answer':
      foreach ($_POST as &$Post) {
        UserHandler::SQLInjectionHandle($Post);
      }
      $TicketHandler = new TicketHandler();
      $IDWriter = (getSessionProp('IDClient')) ? getSessionProp('IDClient') : getSessionProp('IDSupporter');
      $ArMessageType = array('NORMAL' => '0', 'INTERNAL' => '1', 'SYSTEM' => '2', 'SATISFACTION' => '3');
      if (!empty($_FILES['Attachment']['name'])) {
        $TicketHandler->answerTicket($IDWriter,$_POST['IDTicket'],$_POST['TxMessage'],$ArMessageType[$_POST['StMessageType']],$_FILES);
      } else {
        $TicketHandler->answerTicket($IDWriter,$_POST['IDTicket'],$_POST['TxMessage'],$ArMessageType[$_POST['StMessageType']]);
      }
      die("<script>top.submitTicketForm({$_POST['IDTicket']});</script>");
    break;

    case 'preview':
      $TxMessage = TemplateHandler::getPreviewAnswer(getSessionProp('IDUser'), $_POST['TxMessage'], TemplateHandler::IsSupporter());
      die('
          <div id="previewAnswer">
            <h3>Sua Resposta</h3>
            <div class="message">
              '. nl2br($TxMessage) .'
            </div>
          </div>
        ');
    break;
  }
}
?>