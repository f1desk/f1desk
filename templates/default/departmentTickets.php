<?php
	#
	# require the supporter Tickets or the Client Tcikets
	#
	if ( getSessionProp( 'isSupporter' ) == "true" ) {
		require_once( "supporterTickets.php" );
	} else {
		require_once( "clientTickets.php" );
	}
?>