<?php
  require_once(dirname(__FILE__) . '/main.php');  handleLanguage(__FILE__);
  if ( count($_POST) == 0 || !$_POST['action'] ) {
    throw new ErrorHandler(NOT_ACTION);
  }
  $IDNote = ($_POST['IDNote'])?$_POST['IDNote']:"";
  if ( !$IDNote )
    throw new ErrorHandler(NOT_ID);

  switch ( $_POST['action'] ) {
    case 'edit':
      $ArData = array(
        "StTitle" => f1desk_escape_string($_POST['StTitle']),
        "TxNote" => f1desk_escape_string($_POST['TxNote'])
      );
      $ItAffedcted = F1DeskUtils::editNote( $IDNote, $ArData );
      if ( !$ItAffedcted ) {
        throw new ErrorHandler(ERROR_NOTES_EDIT . $IDNote);
      }
    break;

    case 'remove':
      $ItAffedcted = F1DeskUtils::removeNote( $IDNote );
      if ( !$ItAffedcted ) {
        throw new ErrorHandler(ERROR_NOTES_REMOVE . $IDNote);
      }
    break;

    case 'insert':
      if (!get_magic_quotes_gpc()) {
      $ArData = array(
    			"StTitle" => f1desk_escape_string($_POST['StTitle']),
    			"TxNote" => f1desk_escape_string($_POST['TxNote']),
    			"IDSupporter" => getSessionProp('IDSupporter')
  		  );
      } else {
        $ArData = array(
    			"StTitle" => $_POST['StTitle'],
    			"TxNote" => $_POST['TxNote'],
    			"IDSupporter" => getSessionProp('IDSupporter')
  		  );
      }
      $ItAffedcted = F1DeskUtils::createNote( $ArData );
      if ( !$ItAffedcted ) {
        throw new ErrrHandler(ERROR_NOTES_INSERT);
      }
    break;

    default:
      throw new ErrorHandler(ERROR_NONE_ACTION);
    break;
  }

  returnData($_POST['returnType'], $_POST['returnURL']);
?>