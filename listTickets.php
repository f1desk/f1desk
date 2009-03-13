<?php

	require_once(dirname(__FILE__) . '/main.php');
	/*default*/
  handleLanguage(__FILE__);

	#
	# Getting tickets
	#
	$IDDepartment = $_POST['IDDepartment'];
	if ( getSessionProp( 'isSupporter' ) == 'true' ) {
		$ArTickets = TemplateHandler::listTickets( $IDDepartment, getSessionProp('IDSupporter'), getSessionProp('IDUser') );
	} else {
		$ArTickets = TemplateHandler::listClientTickets( getSessionProp('IDUser'), ($IDDepartment=='opened')?true:false );
	}

	require_once(TEMPLATEDIR . 'ticketList.php');

?>