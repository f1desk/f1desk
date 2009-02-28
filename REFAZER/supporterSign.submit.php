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
	
	$my_return = $userHandler->insertSupporter( $ArToInsert, $_POST['ItUnit'], $_POST['ItDepto'] );
	ErrorHandler::debug($my_return); 

?>