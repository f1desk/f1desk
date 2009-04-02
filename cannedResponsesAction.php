<?php
require_once(dirname(__FILE__) . '/main.php');  handleLanguage(__FILE__);
if ( count($_POST) == 0 || !$_POST['action'] ) {
  throw new ErrorHandler(NOT_ACTION);
}
$IDCannedResponse = ($_POST['IDCannedResponse'])?$_POST['IDCannedResponse']:"";
if ( !$IDCannedResponse )
  throw new ErrorHandler(NOT_ID);

switch ( $_POST['action'] ) {
  case 'edit':
    $ArData = array(
      "StTitle" => f1desk_escape_string($_POST['StTitle']),
      "TxMessage" => f1desk_escape_string($_POST['TxMessage'])
    );
    $ItAffedcted = F1DeskUtils::editCannedResponse( $IDCannedResponse, $ArData );
    if ( !$ItAffedcted ) {
      throw new ErrorHandler(ERROR_ID . $IDCannedResponse);
    }
  break;

  case 'remove':
    $ItAffedcted = F1DeskUtils::removeCannedResponse( $IDCannedResponse );
    if ( !$ItAffedcted ) {
      throw new ErrorHandler(ERROR_DELETE);
    }
  break;

  case 'insert':
    if (!get_magic_quotes_gpc()) {
      $ArData = array(
			  "StTitle" => f1desk_escape_string($_POST['StTitle']),
			  "TxMessage" => f1desk_escape_string($_POST['TxMessage']),
			  "BoPersonal" => f1desk_escape_string($_POST['BoPersonal']),
			  "IDSupporter" => getSessionProp('IDSupporter')
		  );
    } else {
      $ArData = array(
    		"StTitle" => $_POST['StTitle'],
    		"TxMessage" => $_POST['TxMessage'],
    		"BoPersonal" => $_POST['BoPersonal'],
    		"IDSupporter" => getSessionProp('IDSupporter')
		  );
    }
    $ItAffedcted = F1DeskUtils::createCannedResponse($ArData);
    if ( !$ItAffedcted ) {
      throw new ErrorHandler(ERROR_INSERT);
    }
  break;

  default:
    throw new ErrorHandler(ERROR_NONE_ACTION);
  break;
}

returnData($_POST['returnType'], $_POST['returnURL']);
?>