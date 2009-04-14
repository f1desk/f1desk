<?php
require_once('main.php');
handleLanguage(__FILE__);

$isSupporter = F1DeskUtils::isSupporter();

/***************************************
 *           Home Actions              *
****************************************/
if(!empty($_POST['StAction'])) {

  switch ($_POST['StAction']) {
    #
    # User's Data Update
    #
  	case 'updateUserData':
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
        ErrorHandler::setNotice('user',USER_ERROR, 'error');
      } else {
        ErrorHandler::setNotice('user',USER_OK, 'ok');
      }
		break;

  	case 'editCannedResponse':
  	  if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
      $ArData = array(
        "StTitle" => f1desk_escape_string($_POST['StTitle']),
        "TxMessage" => f1desk_escape_string($_POST['TxMessage'])
      );
      $ItAffedcted = F1DeskUtils::editCannedResponse( $_POST['IDEdit'], $ArData );
      if ( !$ItAffedcted ) {
        ErrorHandler::setNotice('cannedresponse',ERROR_ID . $_POST['IDEdit'], 'error');
      } else {
        ErrorHandler::setNotice('cannedresponse',CANNED_EDIT_OK, 'ok');
      }
  	break;

  	case 'removeCannedResponse':
  	  if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
      $ItAffedcted = F1DeskUtils::removeCannedResponse( $_POST['IDCannedResponse'] );
      if ( !$ItAffedcted ) {
        ErrorHandler::setNotice('cannedresponse',ERROR_DELETE, 'error');
      } else {
        ErrorHandler::setNotice('cannedresponse',CANNED_DELETE_OK, 'ok');
      }
  	break;

  	case 'createCannedResponse':
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
        ErrorHandler::setNotice('cannedresponse',ERROR_INSERT, 'error');
      } else {
        ErrorHandler::setNotice('cannedresponse',CANNED_INSERT_OK, 'ok');
      }
  	break;

  	case 'createNote':
      if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
      if (!get_magic_quotes_gpc()) {
      $ArData = array(
    			"StTitle" => f1desk_escape_string($_POST['StTitle']),
    			"TxNote" => f1desk_escape_string($_POST['TxMessage']),
    			"IDSupporter" => getSessionProp('IDSupporter')
  		  );
      } else {
        $ArData = array(
    			"StTitle" => $_POST['StTitle'],
    			"TxNote" => $_POST['TxMessage'],
    			"IDSupporter" => getSessionProp('IDSupporter')
  		  );
      }
      $ItAffedcted = F1DeskUtils::createNote( $ArData );
      if ( !$ItAffedcted ) {
        ErrorHandler::setNotice('note',ERROR_NOTES_INSERT, 'error');
      } else {
        ErrorHandler::setNotice('note',NOTE_INSERT_OK, 'ok');
      }
  	break;

  	case 'removeNote':
  	  if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
      $ItAffedcted = F1DeskUtils::removeNote( $_POST['IDNote'] );
      if ( !$ItAffedcted ) {
        ErrorHandler::setNotice('note',ERROR_NOTES_REMOVE . $_POST['IDNote'], 'error');
      } else {
        ErrorHandler::setNotice('note',NOTE_REMOVE_OK, 'ok');
      }
  	break;

  	case 'editNote':
  	  if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
      $ArData = array(
        "StTitle" => f1desk_escape_string($_POST['StTitle']),
        "TxNote" => f1desk_escape_string($_POST['TxMessage'])
      );
      $ItAffedcted = F1DeskUtils::editNote( $_POST['IDEdit'], $ArData );
      if ( !$ItAffedcted ) {
        ErrorHandler::setNotice('note',ERROR_NOTES_EDIT . $_POST['IDEdit'], 'error');
      } else {
        ErrorHandler::setNotice('note',NOTE_EDIT_OK, 'ok');
      }
  	break;

  	case 'removeBookmark':
  	  if (! $isSupporter) { throw new ErrorHandler(INVALID_OPTION); }
  	  $TicketHandler = new TicketHandler();
			$ItAffedcted = $TicketHandler->removeBookmark( getSessionProp('IDSupporter'), $_POST['IDTicket'] );
			if ( !$ItAffedcted ) {
			  ErrorHandler::setNotice('bookmark',ERROR, 'error');
			} else {
			  ErrorHandler::setNotice('bookmark',BOOKMARK_OK, 'ok');
			}
  	break;

  	default:
		  ErrorHandler::setNotice('user',NO_ACTION, 'error');
		break;

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