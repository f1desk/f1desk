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
				"StTitle" => $_POST['StTitle'],
				"StAlias" => $_POST['StAlias'],
				"TxMessage" => $_POST['TxMessage']
			);
			$ItAffedcted = TemplateHandler::editCannedResponse( $IDCannedResponse, $ArData );
			if ( !$ItAffedcted ) {
				die(ERROR_ID . $IDCannedResponse);
			} else {
				die(SUCCESS);
			}
		break;

		case 'remove':
			$ItAffedcted = TemplateHandler::removeCannedResponse( $IDCannedResponse );
			if ( !$ItAffedcted ) {	die('error');	}
			else {	die('sucess');	}
		break;

		case 'insert':
			//$ArCannedResponses = F1desk
			$ItAffedcted = TemplateHandler::createCannedResponse( $_POST['StAlias'], $_POST['StTitle'], $_POST['TxMessage'], getSessionProp('IDSupporter') );
			if ( !$ItAffedcted ) {
				die(ERROR_RESP);
			} else {
				die( $ItAffedcted );
			}
		break;

		default:
			die(ERROR_NONE_ACTION);
		break;
	}
?>