<?php
	session_start();	include_once( "./main.php" );
	$userHandler = new UserHandler();
	
	// Nao podemos simplesmente passar o POST para edição, senao ele ditará para vazio
	// Nao queremos no ArData a confirmação de senha nem o ID
	$ItID = $_POST['ItID']; unset( $_POST['ItID'] ); unset( $_POST['StPasswordConfirmation'] );
	$ArData = array();
	foreach ($_POST as $StField => $StContent) {
		if ( $StContent != "" ) {
			$ArData[ $StField ] = $StContent;
		}
	}
	
	$ArRowUpdated = $userHandler->updateClient( $ArData, $ItID, "user" );
	
	echo "<b>Updated Row:</b><br/>";	ErrorHandler::debug($ArRowUpdated);
?>