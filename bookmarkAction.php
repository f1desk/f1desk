<?php
	require_once(dirname(__FILE__) . '/main.php');
	if ( count($_POST) == 0 || !$_POST['action'] ) {
		die('Erro: Nenhuma ação a ser realizada');
	}
	$IDTicket = ($_POST['IDTicket'])?$_POST['IDTicket']:"";
	if ( !$IDTicket ) die('Erro: ID não informado.');
	
	switch ( $_POST['action'] ) {
		case 'remove':
			$ItAffedcted = TemplateHandler::removeBookmark( $IDTicket, getSessionProp('IDSupporter') );
			if ( !$ItAffedcted ) {	die('error');	} 
			else {	die('sucess');	}
		break;
		
		default:
			die('Erro: Nenhuma ação a ser realizada');
		break;
	}
?>