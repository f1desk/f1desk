<?php
	require_once(dirname(__FILE__) . '/main.php');

	$ArData = array();
	#
	# We need to know what we have to update, otherwise it would clear and overwrite
	#
	if ( key_exists( 'StName', $_POST ) ) {
		$ArData['StName'] = f1desk_escape_string($_POST['StName']);
	}

	if ( key_exists( 'StEmail', $_POST ) ) {
		$ArData['StEmail'] = f1desk_escape_string($_POST['StEmail']);
	}

	if ( key_exists( 'TxHeader', $_POST ) ) {
		$ArData['TxHeader'] = f1desk_escape_string($_POST['TxHeader']);
	}

	if ( key_exists( 'TxSign', $_POST ) ) {
		$ArData['TxSign'] = f1desk_escape_string($_POST['TxSign']);
	}

	$ItAffected = TemplateHandler::updateUserData( getSessionProp('IDUser'), $ArData );
	if ( !$ItAffected ) {	die('error');	}
	else {	die('sucess');  }

?>