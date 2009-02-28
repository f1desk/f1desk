<?php
	session_start();	include_once( "./main.php" );
	$userHandler = new UserHandler();
	
	//ErrorHandler::debug($_POST);
	// Nao podemos simplesmente passar o POST para edição, senao ele editará para vazio
	// Nao queremos no ArData a confirmação de senha nem o ID
	$ItIDSupporter = $_POST['ItID']; unset( $_POST['ItID'] ); 
	
	$ItIDUnit = $_POST['IDUnit'];	unset( $_POST['IDUnit'] );
	$ItIDDepartment = $_POST['IDDepto'];	unset( $_POST['IDDepto'] );
	if ( isset( $_POST['StPasswordConfirmation'] ) ) {
		unset( $_POST['StPasswordConfirmation'] );
	}
	$ArData = array();
	foreach ($_POST as $StField => $StContent) {
		if ( $StContent != "" ) {
			$ArData[ $StField ] = $StContent;
		}
	}
	
	$ArRowUpdated = $userHandler->updateSupporter(  $ArData, $ItIDSupporter, $ItIDUnit, $ItIDDepartment	);
	echo "<b>Updated Row:</b><br/>";	ErrorHandler::debug($ArRowUpdated);
?>