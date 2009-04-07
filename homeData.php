<?php
require_once('main.php');

handleLanguage(__FILE__);

$isSupporter = F1DeskUtils::isSupporter();

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
      ErrorHandler::setNotice(USER_ERROR, 'error');
    } else {
      ErrorHandler::setNotice(USER_OK, 'ok');
    }
  } elseif ($_POST['StArea'] == 'CannedResponses') {

    #
    # Canned Response's Action
    #
    $IDCannedResponse = ($_POST['IDCannedResponse']) ? $_POST['IDCannedResponse'] : '';
    if ( !$IDCannedResponse ) {
      ErrorHandler::setNotice(NOT_ID, 'error');
    }

    switch ( $_POST['StAction'] ) {
      case 'edit':
        if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
        $ArData = array(
          "StTitle" => f1desk_escape_string($_POST['StTitle']),
          "TxMessage" => f1desk_escape_string($_POST['TxMessage'])
        );
        $ItAffedcted = F1DeskUtils::editCannedResponse( $IDCannedResponse, $ArData );
        if ( !$ItAffedcted ) {
          ErrorHandler::setNotice(ERROR_ID . $IDCannedResponse, 'error');
        } else {
          ErrorHandler::setNotice(CANNED_EDIT_OK, 'ok');
        }
      break;

      case 'remove':
        if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
        $ItAffedcted = F1DeskUtils::removeCannedResponse( $IDCannedResponse );
        if ( !$ItAffedcted ) {
          ErrorHandler::setNotice(ERROR_DELETE, 'error');
        } else {
          ErrorHandler::setNotice(CANNED_DELETE_OK, 'ok');
        }
      break;

      case 'insert':
        if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
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
          ErrorHandler::setNotice(ERROR_INSERT, 'error');
        } else {
          ErrorHandler::setNotice(CANNED_INSERT_OK, 'ok');
        }
      break;

      default:
        ErrorHandler::setNotice(ERROR_NONE_ACTION, 'error');
      break;
    }
  } elseif ($_POST['StArea'] == 'Notes') {

    #
    # Note's Action
    #
    $IDNote = ($_POST['IDNote']) ? $_POST['IDNote'] : '';
    if ( !$IDNote ) {
      ErrorHandler::setNotice(NOT_ID, 'error');
    }

    switch ( $_POST['StAction'] ) {
      case 'edit':
        if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
        $ArData = array(
          "StTitle" => f1desk_escape_string($_POST['StTitle']),
          "TxNote" => f1desk_escape_string($_POST['TxNote'])
        );
        $ItAffedcted = F1DeskUtils::editNote( $IDNote, $ArData );
        if ( !$ItAffedcted ) {
          ErrorHandler::setNotice(ERROR_NOTES_EDIT . $IDNote, 'error');
        } else {
          ErrorHandler::setNotice(NOTE_EDIT_OK, 'ok');
        }
      break;

      case 'remove':
        if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
        $ItAffedcted = F1DeskUtils::removeNote( $IDNote );
        if ( !$ItAffedcted ) {
          ErrorHandler::setNotice(ERROR_NOTES_REMOVE . $IDNote, 'error');
        } else {
          ErrorHandler::setNotice(NOTE_REMOVE_OK, 'ok');
        }
      break;

      case 'insert':
        if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
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
          ErrorHandler::setNotice(ERROR_NOTES_INSERT, 'error');
        } else {
          ErrorHandler::setNotice(NOTE_INSERT_OK, 'ok');
        }
      break;

      default:
        ErrorHandler::setNotice(ERROR_NONE_ACTION, 'error');
      break;
    }
  } elseif ($_POST['StArea'] == 'Bookmark') {

    #
    # Bookmarked Ticket's Actions
    #
    $IDTicket = ($_POST['IDTicket']) ? $_POST['IDTicket'] : '';
  	if ( !$IDTicket ) {
  	  ErrorHandler::setNotice(NO_ID, 'error');
  	}

  	switch ( $_POST['StAction'] ) {

  		case 'remove':
  		  if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
  			$ItAffedcted = F1DeskUtils::removeBookmark( $IDTicket, getSessionProp('IDSupporter') );
  			if ( !$ItAffedcted ) {
  			  ErrorHandler::setNotice(ERROR, 'error');
  			} else {
  			  ErrorHandler::setNotice(BOOKMARK_OK, 'ok');
  			}
  		break;

  		default:
  		  ErrorHandler::setNotice(NO_ACTION, 'error');
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
if ( $isSupporter ){
	$ArUser = F1DeskUtils::getUserData( getSessionProp('IDSupporter'), 0);

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

} else {
	$ArUser = F1DeskUtils::getUserData( getSessionProp('IDClient'), 1);
}


?>