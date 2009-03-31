<?php
  require_once(dirname(__FILE__) . '/main.php');  handleLanguage(__FILE__);
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
      $ItAffedcted = F1DeskUtils::editNote( $IDNote, $ArData );
      if ( !$ItAffedcted ) {
        die(ERROR_NOTES_EDIT . $IDNote);
      }
    break;

    case 'remove':
      $ItAffedcted = F1DeskUtils::removeNote( $IDNote );
      if ( !$ItAffedcted ) {  
        die(ERROR_NOTES_REMOVE . $IDNote);
      }
    break;

    case 'insert':
      $ArData = array(
  			"StTitle" => f1desk_escape_string($_POST['StTitle']),
  			"TxNote" => f1desk_escape_string($_POST['TxNote']),
  			"IDSupporter" => getSessionProp('IDSupporter')
  		);
      $ItAffedcted = F1DeskUtils::createNote( $ArData );
      if ( !$ItAffedcted ) {
        die(ERROR_NOTES_INSERT);
      }
    break;

    default:
      die(ERROR_NONE_ACTION);
    break;
  }
  
  returnData($_POST['returnType'], $_POST['returnURL']);
?>