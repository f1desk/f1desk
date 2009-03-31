<?php
require_once(dirname(__FILE__) . '/main.php');  handleLanguage(__FILE__);
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
    $ItAffedcted = F1DeskUtils::editCannedResponse( $IDCannedResponse, $ArData );
    if ( !$ItAffedcted ) {
      die(ERROR_ID . $IDCannedResponse);
    }
  break;

  case 'remove':
    $ItAffedcted = F1DeskUtils::removeCannedResponse( $IDCannedResponse );
    if ( !$ItAffedcted ) {  
      die(ERROR_DELETE);  
    }
  break;

  case 'insert':
    $ArData = array(
			"StTitle" => f1desk_escape_string($_POST['StTitle']),
			"TxMessage" => f1desk_escape_string($_POST['TxMessage']),
			"BoPersonal" => f1desk_escape_string($_POST['BoPersonal']),
			"IDSupporter" => getSessionProp('IDSupporter')
		);
    $ItAffedcted = F1DeskUtils::createCannedResponse($ArData);
    if ( !$ItAffedcted ) {
      die(ERROR_INSERT);
    }
  break;

  default:
    die(ERROR_NONE_ACTION);
  break;
}

returnData($_POST['returnType'], $_POST['returnURL']);
?>