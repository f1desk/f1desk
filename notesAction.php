<?php
  require_once(dirname(__FILE__) . '/main.php');
  if ( count($_POST) == 0 || !$_POST['action'] ) {
    die(NOT_ACTION);
  }
  $IDNote = ($_POST['IDNote'])?$_POST['IDNote']:"";
  if ( !$IDNote ) die(NOT_ID);

  switch ( $_POST['action'] ) {
    case 'edit':
      $ArData = array(
        "StTitle" => f1desk_escape_string($_POST['StTitle']),
        "TxNote" => f1desk_escape_string($_POST['TxNote'])
      );
      $ItAffedcted = TemplateHandler::editNote( $IDNote, $ArData );
      if ( !$ItAffedcted ) {
        die('Erro ao editar anotação. ID -> ' . $IDNote);
      } else {
        die("
          <td class='TicketNumber'>
            ".$_POST['StTitle']."
            <input type='hidden' id='StNoteTitle$IDNote' value='".f1desk_escape_string($_POST['StTitle'], false, true)."' >
          </td>
          <td>
            <input type='hidden' id='TxNote$IDNote' value='".f1desk_escape_string($_POST['TxNote'], false, true)."'>
            <img src='". TEMPLATEDIR ."images/button_edit.png' alt='Editar' class='cannedAction' onclick='Home.startEditElement(\"note\", $IDNote);'>
            <img src='". TEMPLATEDIR ."images/button_cancel.png' alt='Remover' class='cannedAction' onclick='Home.removeNote($IDNote)'>
            <img src='". TEMPLATEDIR ."images/visualizar.png' alt='Visualizar' class='cannedAction' onclick='flowWindow.previewNote(\"". f1desk_escape_string($_POST['StTitle'], false, true)."\", \"". f1desk_escape_string($_POST['TxNote'], true, true)."\")' >
          </td>
        ");
      }
    break;

    case 'remove':
      $ItAffedcted = TemplateHandler::removeNote( $IDNote );
      if ( !$ItAffedcted ) {  die('error');  }
      else {  die('sucess');  }
    break;

    case 'insert':
      $ItAffedcted = TemplateHandler::createNote( f1desk_escape_string($_POST['StTitle']), f1desk_escape_string($_POST['TxNote']), getSessionProp('IDSupporter') );
      if ( !$ItAffedcted ) {
        die(ERROR_RESP);
      } else {
        die("
          <tr id='noteTR$ItAffedcted'>
            <td class='TicketNumber'>
              ".$_POST['StTitle']."
              <input type='hidden' id='StNoteTitle$ItAffedcted' value='".f1desk_escape_string($_POST['StTitle'], false, true)."' >
            </td>
            <td>
              <input type='hidden' id='TxNote$ItAffedcted' value='".f1desk_escape_string($_POST['TxNote'], false, true)."'>
              <img src='". TEMPLATEDIR ."images/button_edit.png' alt='Editar' class='cannedAction' onclick='Home.startEditElement(\"note\", $ItAffedcted);'>
              <img src='". TEMPLATEDIR ."images/button_cancel.png' alt='Remover' class='cannedAction' onclick='Home.removeNote($ItAffedcted)'>
              <img src='". TEMPLATEDIR ."images/visualizar.png' alt='Visualizar' class='cannedAction' onclick='flowWindow.previewNote(\"". f1desk_escape_string($_POST['StTitle'], false, true)."\", \"". f1desk_escape_string($_POST['TxNote'], true, true)."\")' >
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