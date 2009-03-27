<?php
require_once(dirname(__FILE__) . '/main.php');
if ( count($_POST) == 0 || !$_POST['action'] ) {
  die(NOT_ACTION);
}
$IDCannedResponse = ($_POST['IDCannedResponse'])?$_POST['IDCannedResponse']:"";
if ( !$IDCannedResponse ) die(NOT_ID);

switch ( $_POST['action'] ) {
  case 'edit':
    $ArData = array(
    "StTitle" => f1desk_escape_string($_POST['StTitle']),
    "TxMessage" => f1desk_escape_string($_POST['TxMessage'])
    );
    $ItAffedcted = TemplateHandler::editCannedResponse( $IDCannedResponse, $ArData );
    if ( !$ItAffedcted ) {
      die(ERROR_ID . $IDCannedResponse);
    } else {
      die("
            <td class='TicketNumber'>
              ".$_POST['StTitle']."
              <input id='StCannedTitle$IDCannedResponse' type='hidden' value='".f1desk_escape_string($_POST['StTitle'], false, true)."'/>
            </td>
            <td>
              <input id='TxCannedResponse$IDCannedResponse' type='hidden' value='". f1desk_escape_string($_POST['TxMessage'], false, true) ."'/>
              <img class='cannedAction' onclick='HOME.startEditElement(\"canned\", $IDCannedResponse);' alt='Editar' src='templates/default/images/button_edit.png'/>
              <img class='cannedAction' onclick='HOME.removeCannedResponse($IDCannedResponse)' alt='Remover' src='templates/default/images/button_cancel.png'/>
              <img id='previemCanned$IDCannedResponse' class='cannedAction' onclick='flowWindow.previewCannedResponse( \"". f1desk_escape_string($_POST['StTitle'], false, true) ."\", \"". f1desk_escape_string($_POST['TxMessage'], true, true) ."\")' src='templates/default/images/visualizar.png'/>
            </td>
        ");
    }
    break;

  case 'remove':
    $ItAffedcted = TemplateHandler::removeCannedResponse( $IDCannedResponse );
    if ( !$ItAffedcted ) {  die('error');  }
    else {  die('sucess');  }
    break;

  case 'insert':
    $ItAffedcted = TemplateHandler::createCannedResponse( f1desk_escape_string($_POST['StTitle']), f1desk_escape_string($_POST['TxMessage']), getSessionProp('IDSupporter') );
    if ( !$ItAffedcted ) {
      die(ERROR_RESP);
    } else {
      die("
            <tr id=\"cannedTR$ItAffedcted\">
              <td class='TicketNumber'>
                ".$_POST['StTitle']."
                <input id='StCannedTitle$ItAffedcted' type='hidden' value='".f1desk_escape_string($_POST['StTitle'], false, true)."'/>
              </td>
              <td>
                <input id='TxCannedResponse$ItAffedcted' type='hidden' value='". f1desk_escape_string($_POST['TxMessage'], false, true) ."'/>
                <img class='cannedAction' onclick='HOME.startEditElement(\"canned\", $ItAffedcted);' alt='Editar' src='templates/default/images/button_edit.png'/>
                <img class='cannedAction' onclick='HOME.removeCannedResponse($ItAffedcted)' alt='Remover' src='templates/default/images/button_cancel.png'/>
                <img id='previemCanned$ItAffedcted' class='cannedAction' onclick='flowWindow.previewCannedResponse(\"". f1desk_escape_string($_POST['StTitle'], false, true) ."\", \"". f1desk_escape_string($_POST['TxMessage'], true, true) ."\")' src='templates/default/images/visualizar.png'/>
              </td>
            </tr>
          ");
    }
    break;

  default:
    die(ERROR_NONE_ACTION);
    break;
}
?>