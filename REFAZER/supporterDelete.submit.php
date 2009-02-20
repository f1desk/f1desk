<?php
	session_start();	include_once( "./main.php" );
	$userHandler = new UserHandler();
	
	if ( isset( $_POST['IDUser'] ) ) {
		$userHandler->deleteClient( $_POST['IDUser'], "supporter" );
	}
	
	ErrorHandler::debug( "Supporter removed sucessfully!" );
?>