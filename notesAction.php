<?php
	require_once(dirname(__FILE__) . '/main.php');
	if ( count($_POST) == 0 || !$_POST['action'] ) {
		die('Erro: Nenhuma ação a ser realizada');
	}
	$IDNote = ($_POST['IDNote'])?$_POST['IDNote']:"";
	if ( !$IDNote ) die('Erro: ID não informado.');
	
	switch ( $_POST['action'] ) {
		case 'edit':
			$ArData = array(
				"StTitle" => $_POST['StTitle'],
				"TxNote" => $_POST['TxNote']
			);
			$ItAffedcted = TemplateHandler::editNote( $IDNote, $ArData );
			if ( !$ItAffedcted ) {
				die('Erro ao editar anotação. ID -> ' . $IDNote);
			} else {
				die('Atualizado com sucesso!');
			}
		break;
		
		case 'remove':
			$ItAffedcted = TemplateHandler::removeNote( $IDNote );
			if ( !$ItAffedcted ) {	die('error');	} 
			else {	die('sucess');	}
		break;
		
		case 'insert':
			$ItAffedcted = TemplateHandler::createNote( $_POST['StTitle'], $_POST['TxNote'], getSessionProp('IDSupporter') );
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