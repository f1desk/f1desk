<?php
require_once('main.php');
handleLanguage(__FILE__);

/***************************************
 *           Home Actions              *
****************************************/

if(!empty($_POST['StArea']) && !empty($_POST['StAction'])) {

  if ($_POST['StArea'] == 'User' && $_POST['StAction'] == 'Update') {

    #
    # User's Data Update
    #
    $ArData = array();
    if (array_key_exists( 'StName', $_POST )) {
    	$ArData['StName'] = f1desk_escape_string($_POST['StName']);
    }

    if (array_key_exists('StPassword',$_POST) && ! empty($_POST['StPassword'])) {
      $ArData['StPassword'] = $_POST['StPassword'];
    }

    if (array_key_exists( 'StEmail', $_POST )) {
    	$ArData['StEmail'] = f1desk_escape_string($_POST['StEmail']);
    }

    if (array_key_exists( 'BoNotify', $_POST )) {
    	$ArData['BoNotify'] = $_POST['BoNotify'];
    }

    if (array_key_exists( 'TxHeader', $_POST )) {
    	$ArData['TxHeader'] = f1desk_escape_string($_POST['TxHeader']);
    }

    if (array_key_exists( 'TxSign', $_POST )) {
    	$ArData['TxSign'] = f1desk_escape_string($_POST['TxSign']);
    }

    $UserHanlder = new UserHandler();
    $ItAffedcted = $UserHanlder->updateUser($ArData, getSessionProp('IDUser'));
    if ($ItAffedcted < 0) {
      $returnMessage = USER_ERROR;
      $returnType = 'error';
    } else {
      $returnMessage = USER_OK;
      $returnType = 'ok';
    }
  } elseif ($_POST['StArea'] == 'CannedResponses') {

    #
    # Canned Response's Action
    #
    $IDCannedResponse = ($_POST['IDCannedResponse']) ? $_POST['IDCannedResponse'] : '';
    if ( !$IDCannedResponse ) {
      $returnMessage = NOT_ID;
      $returnType = 'error';
    }

    switch ( $_POST['StAction'] ) {
      case 'edit':
        $ArData = array(
          "StTitle" => f1desk_escape_string($_POST['StTitle']),
          "TxMessage" => f1desk_escape_string($_POST['TxMessage'])
        );
        $ItAffedcted = F1DeskUtils::editCannedResponse( $IDCannedResponse, $ArData );
        if ( !$ItAffedcted ) {
          $returnMessage = ERROR_ID . $IDCannedResponse;
          $returnType = 'error';
        } else {
          $returnMessage = CANNED_EDIT_OK;
          $returnType = 'ok';
        }
      break;

      case 'remove':
        $ItAffedcted = F1DeskUtils::removeCannedResponse( $IDCannedResponse );
        if ( !$ItAffedcted ) {
          $returnMessage = ERROR_DELETE;
          $returnType = 'error';
        } else {
          $returnMessage = CANNED_DELETE_OK;
          $returnType = 'ok';
        }
      break;

      case 'insert':
        if (!get_magic_quotes_gpc()) {
          $ArData = array(
    			  "StTitle" => f1desk_escape_string($_POST['StTitle']),
    			  "TxMessage" => f1desk_escape_string($_POST['TxMessage']),
    			  "BoPersonal" => f1desk_escape_string($_POST['BoPersonal']),
    			  "IDSupporter" => getSessionProp('IDSupporter')
    		  );
        } else {
          $ArData = array(
        		"StTitle" => $_POST['StTitle'],
        		"TxMessage" => $_POST['TxMessage'],
        		"BoPersonal" => $_POST['BoPersonal'],
        		"IDSupporter" => getSessionProp('IDSupporter')
    		  );
        }
        $ItAffedcted = F1DeskUtils::createCannedResponse($ArData);
        if ( !$ItAffedcted ) {
          $returnMessage = ERROR_INSERT;
          $returnType = 'error';
        } else {
          $returnMessage = CANNED_INSERT_OK;
          $returnType = 'ok';
        }
      break;

      default:
        $returnMessage = ERROR_NONE_ACTION;
        $returnType = 'error';
      break;
    }
  } elseif ($_POST['StArea'] == 'Notes') {

    #
    # Note's Action
    #
    $IDNote = ($_POST['IDNote']) ? $_POST['IDNote'] : '';
    if ( !$IDNote ) {
      $returnMessage = NOT_ID;
      $returnType = 'error';
    }

    switch ( $_POST['StAction'] ) {
      case 'edit':
        $ArData = array(
          "StTitle" => f1desk_escape_string($_POST['StTitle']),
          "TxNote" => f1desk_escape_string($_POST['TxNote'])
        );
        $ItAffedcted = F1DeskUtils::editNote( $IDNote, $ArData );
        if ( !$ItAffedcted ) {
          $returnMessage = ERROR_NOTES_EDIT . $IDNote;
          $returnType = 'error';
        } else {
          $returnMessage = NOTE_EDIT_OK;
          $returnType = 'ok';
        }
      break;

      case 'remove':
        $ItAffedcted = F1DeskUtils::removeNote( $IDNote );
        if ( !$ItAffedcted ) {
          $returnMessage = ERROR_NOTES_REMOVE . $IDNote;
          $returnType = 'error';
        } else {
          $returnMessage = NOTE_REMOVE_OK;
          $returnType = 'ok';
        }
      break;

      case 'insert':
        if (!get_magic_quotes_gpc()) {
        $ArData = array(
      			"StTitle" => f1desk_escape_string($_POST['StTitle']),
      			"TxNote" => f1desk_escape_string($_POST['TxNote']),
      			"IDSupporter" => getSessionProp('IDSupporter')
    		  );
        } else {
          $ArData = array(
      			"StTitle" => $_POST['StTitle'],
      			"TxNote" => $_POST['TxNote'],
      			"IDSupporter" => getSessionProp('IDSupporter')
    		  );
        }
        $ItAffedcted = F1DeskUtils::createNote( $ArData );
        if ( !$ItAffedcted ) {
          throw new ErrrHandler(ERROR_NOTES_INSERT);
        } else {
          $returnMessage = NOTE_INSERT_OK;
          $returnType = 'ok';
        }
      break;

      default:
        $returnMessage = ERROR_NONE_ACTION;
        $returnType = 'error';
      break;
    }
  } elseif ($_POST['StArea'] == 'Bookmark') {

    #
    # Bookmarked Ticket's Actions
    #
    $IDTicket = ($_POST['IDTicket']) ? $_POST['IDTicket'] : '';
  	if ( !$IDTicket ) {
  	  $returnMessage = NO_ID;
  	  $returnType = 'error';
  	}

  	switch ( $_POST['StAction'] ) {

  		case 'remove':
  			$ItAffedcted = F1DeskUtils::removeBookmark( $IDTicket, getSessionProp('IDSupporter') );
  			if ( !$ItAffedcted ) {
  			  $returnMessage = ERROR;
  			  $returnType = 'error';
  			} else {
  			  $returnMessage = BOOKMARK_OK;
  			  $returnType = 'ok';
  			}
  		break;

  		default:
  			$returnMessage = NO_ACTION;
  			$returnType = 'error';
  		break;
  	}
  }
}

/***************************************
 *           Home data                 *
****************************************/

#
# User's data
#
if ( getSessionProp('isSupporter') == "true" ){
	$ArUser = TemplateHandler::getUserData( getSessionProp('IDSupporter'), 0);
} else {
	$ArUser = TemplateHandler::getUserData( getSessionProp('IDClient'), 1);
}

#
# Canned response's data
#
$ArCannedResponses = F1DeskUtils::listCannedResponses(getSessionProp('IDSupporter'));

#
# Note's Data
#
$ArNotes = F1DeskUtils::listNotes(getSessionProp('IDSupporter'));

#
# Bookmarked Ticket's data
#
$ArBookmark = F1DeskUtils::listSupporterBookmark(getSessionProp('IDSupporter'));
?>