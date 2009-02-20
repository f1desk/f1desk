<?php
	require_once(dirname(__FILE__) . '/main.php');
	/*default*/
  handleLanguage(__FILE__);

	#
	# Getting tickets
	#
	$IDDepartment = $_POST['IDDepartment'];
	if ( $_POST['StUser'] == "supporter" ) {
		$ArTickets = TemplateHandler::listTickets( $IDDepartment, getSessionProp("IDSupporter") );
		$ItReadCount = TemplateHandler::notReadCount( $IDDepartment, getSessionProp("IDSupporter"), "supporter" );
	} else {
		$ArTickets = TemplateHandler::listClientTickets( getSessionProp('IDUser'), ($IDDepartment=="opened")?true:false );
		$ArReadCount = TemplateHandler::notReadCount( $IDDepartment, getSessionProp("IDUser"), "client" );
		$ItReadCount = $ArReadCount[ $IDDepartment ];
	}

	#
	# Generate JSON!!!! \o/
	#
	$returnJSON = array();
	foreach ( $ArTickets as $ticketProperties ){
		$returnJSON['TicketList'][] = array(
			"Number" => $ticketProperties['IDTicket'],
			"Title" => $ticketProperties['StTitle'],
			"Supporter" => (isset($ticketProperties['StSupporterName']))?$ticketProperties['StSupporterName']:$ticketProperties['StSupporter'],
			"Status" => (isset($ticketProperties['isRead']))? $ticketProperties['isRead'] : $ticketProperties['StSituation']
		);
	}

	if (empty($returnJSON)) {
	  $returnJSON['TicketList'][0] = array(
	   'emptyMessage' => NO_CALLS
	  );
	}

	#
	# notReadCount by Department
	#
	$returnJSON['notReadCount'] = $ItReadCount;

	die(json_encode( $returnJSON ));


?>