<?php
	require_once(dirname(__FILE__) . '/main.php');
	if ( count($_POST) == 0 || !$_POST['action'] ) {
		die('Erro: Nenhuma ação a ser realizada');
	}
	$IDCannedResponse = ($_POST['IDCannedResponse'])?$_POST['IDCannedResponse']:"";
	if ( !$IDCannedResponse ) die('Erro: ID não informado.');
	
	switch ( $_POST['action'] ) {
		case 'edit':
			$ArData = array(
				"StTitle" => $_POST['StTitle'],
				"StAlias" => $_POST['StAlias'],
				"TxMessage" => $_POST['TxMessage']
			);
			$ItAffedcted = TemplateHandler::editCannedResponse( $IDCannedResponse, $ArData );
			if ( !$ItAffedcted ) {
				die('Erro ao editar resposta pronta. ID -> ' . $IDCannedResponse);
			} else {
				die('Atualizado com sucesso!');
			}
		break;
		
		case 'remove':
			$ItAffedcted = TemplateHandler::removeCannedResponse( $IDCannedResponse );
			if ( !$ItAffedcted ) {	die('error');	} 
			else {	die('sucess');	}
		break;
		
		case 'insert':
			$ItAffedcted = TemplateHandler::createCannedResponse( $_POST['StAlias'], $_POST['StTitle'], $_POST['TxMessage'], getSessionProp('IDSupporter') );
			if ( !$ItAffedcted ) {
				die('Erro ao criar resposta. Por favor, tente novamente');
			} else {
				die( $ItAffedcted );
			}
		break;
	
		default:
			die('Erro: Nenhuma ação a ser realizada');
		break;
	}
?>