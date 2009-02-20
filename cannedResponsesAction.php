<?php
	require_once(dirname(__FILE__) . '/main.php');
	if ( count($_POST) == 0 || !$_POST['action'] ) {
		die('Erro: Nenhuma ação a ser realizada');
	}
	
	switch ( $_POST['action'] ) {
		case 'edit':
			$IDCannedResponse = $_POST['IDCannedResponse'];
			if ( !$IDCannedResponse ) die('Erro: ID não informado.');
			$ArData = array(
				"StTitle" => $_POST['StTitle'],
				"StAlias" => $_POST['StAlias'],
				"TxMessage" => utf8_encode($_POST['TxMessage'])
			);
			$ItAffedcted = TemplateHandler::editCannedResponse( $IDCannedResponse, $ArData );
			if ( !$ItAffedcted ) {
				die('Erro ao editar resposta pronta. ID -> ' . $IDCannedResponse);
			} else {
				die('Atualizado com sucesso!');
			}
		break;
	
		default:
			die('Erro: Nenhuma ação a ser realizada');
		break;
	}
?>