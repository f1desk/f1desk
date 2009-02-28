<?php

	session_start();		include_once( "./main.php" );
	$userHandler = new UserHandler();
	
	#
	# Formating date
	#
	Validate::Email( $_POST['StEmail'] );
	
	$ArToInsert = array(
		"StName" => $_POST['StName'],
		"StEmail" => $_POST['StEmail'],
		"StPassword" => $_POST['StPassword'],
	);
	
	$my_return = $userHandler->insertClient( $ArToInsert );
	ErrorHandler::debug($my_return);

?>