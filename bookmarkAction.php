<?php
	require_once(dirname(__FILE__) . '/main.php');
	handleLanguage(__FILE__);
	if ( count($_POST) == 0 || !$_POST['action'] ) {
		throw new ErrorHandler(NO_ACTION);
	}
	$IDTicket = ($_POST['IDTicket'])?$_POST['IDTicket']:"";
	if ( !$IDTicket ) throw new ErrorHandler(NO_ID);

	switch ( $_POST['action'] ) {
		case 'remove':
			$ItAffedcted = F1DeskUtils::removeBookmark( $IDTicket, getSessionProp('IDSupporter') );
			if ( !$ItAffedcted ) {
			  throw new ErrorHandler(ERROR);
			}
		break;

		default:
			throw new ErrorHandler(NO_ACTION);
		break;
	}

	returnData($_POST['returnType'],$_POST['returnURL']);
?>