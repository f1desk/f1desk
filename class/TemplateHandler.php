<?php

/**
 * class to handle templates
 *
 */
abstract class TemplateHandler {

  private static $ArInstances = array();
  public static $CurrentPage = '';

  /**
   * get instances of another classes
   *
   * @param string $StClassName
   * @return resource
   */
  public static function getInstance($StClassName) {
    if (! array_key_exists($StClassName, self::$ArInstances)) {
      $ArInstances[$StClassName] = new $StClassName;
    }

    return $ArInstances[$StClassName];
  }

  /**
   * list the departments with their subdepartments
   *
   * @return array
   */
  public static function getDepartments( $IDSupporter, $Format = false ) {
    $ArFormatted = array();

    $ArDepartments = F1DeskUtils::getDepartments($IDSupporter);
    $ArSubDepartments = F1DeskUtils::getSubDepartments($IDSupporter);

    foreach ($ArDepartments as $IDDepartment => $StDepartment) {
      if (array_key_exists($IDDepartment,$ArSubDepartments) === true) {
        $ArSubs = $ArSubDepartments[$IDDepartment];
      } else {
        $ArSubs = array();
      }

      $ArFormatted[$IDDepartment] = array(
        'ID' => $IDDepartment,
        'StDepartment' => $StDepartment,
        'ArSubDepartments' => $ArSubs
      );
    }

    #
    # formatting
    #
    if ($Format == true) {
      foreach ($ArSubDepartments as $IDDepartment => $ArSubDepartments) {
        foreach ($ArSubDepartments as $IDSubDepartments) {
          if (array_key_exists($IDSubDepartments,$ArDepartments) === true) {
            $ArFormatted[$IDSubDepartments] = array(
              'ID' => $IDSubDepartments,
              'StDepartment' => $ArDepartments[$IDDepartment] . '::' . $ArDepartments[$IDSubDepartments]
            );
          }
        }
      }
    }

    return $ArFormatted;
  }

  /**
   * list the departments of an user
   *
   * @return array
   */
  public static function getUserDepartments() {
    $ArDepartment = array();

    $ArDepartment['opened'] = array (
  		'ID' => 'opened',
  		'StDepartment' => OPENEDCALLS,
  		'SubDepartment' => array()
  	);

  	$ArDepartment['closed'] = array (
  		'ID' => 'closed',
  		'StDepartment' => CLOSEDCALLS,
  		'SubDepartment' => array()
  	);

  	return $ArDepartment;
  }

  /**
   * List all tickets with the opened situation by a department given
   *
   * @param integer $IDDepartment
   * @return array
   */
  public static function listTickets( $IDDepartment, $IDSupporter, $IDUser ){

  	$ObTicket = self::getInstance( "TicketHandler" );
  	if ($IDDepartment != 'ignored' && $IDDepartment != 'bookmark') {
    	$openTickets = $ObTicket->listTickets( $IDDepartment );
    	$ignoredTickets = $ObTicket->listIgnoredTickets($IDSupporter);
    	$readTickets = $ObTicket->getReadTickets($IDDepartment, $IDUser);


      foreach ($openTickets as $IDTicket => &$ArTicket) {
        if (array_key_exists($IDTicket,$ignoredTickets) == true) {
          unset($openTickets[$IDTicket]);
          continue;
        }

        if (array_key_exists($IDTicket,$readTickets) == true) {
          $ArTicket['isRead'] = 1;
        } else {
          $ArTicket['isRead'] = 0;
        }
      }
  	} elseif ($IDDepartment == 'ignored') {
  	  $openTickets = $ObTicket->listIgnoredTickets($IDSupporter);
  	  foreach ($openTickets as $IDTicket => &$ArTicket) {
        $ArTicket['isRead'] = 1;
  	  }
  	} else {
  	  $TicketList = array();
  	  $openTickets = $ObTicket->listBookmarkTickets($IDSupporter);
  	  $TicketList = array_keys($openTickets);
  	  $readTickets = $ObTicket->getReadTickets($IDDepartment, $IDUser, $TicketList);
  	  foreach ($openTickets as $IDTicket => &$ArTicket) {
  	    if (array_key_exists($IDTicket,$readTickets) == true) {
          $ArTicket['isRead'] = 1;
        } else {
          $ArTicket['isRead'] = 0;
        }
  	  }

  	}

  	return $openTickets;
  }

  /**
   *
   *
   * @param unknown_type $IDUser
   * @param unknown_type $BoOpened
   * @return unknown
   */
  public static function listClientTickets( $IDUser, $BoOpened = true ) {

  	$ObTicket = self::getInstance( "TicketHandler" );
  	if ($BoOpened == true) {
  	  $IDDepartment = 'opened';
  	} else {
  	  $IDDepartment = 'closed';
  	}
  	$openTickets = $ObTicket->listClientTickets( $IDUser, $BoOpened );
  	$readTickets = $ObTicket->getUserReadTickets($IDUser);
  	
  	foreach ($openTickets as $IDTicket => &$ArTicket) {
        if (array_key_exists($IDTicket,$readTickets) == true) {
          $ArTicket['isRead'] = 1;
        } else {
          $ArTicket['isRead'] = 0;
        }
      }
  	return $openTickets;

  }

  /**
   * Return a list of all supporters by ticket
   *
   * @return array
   */
  public static function listSupporters($IDTicket) {

    $ArSupporters = array();
    $ObjUser = self::getInstance('UserHandler');
  	list($ArSupporters1,$ArSupporters2) = $ObjUser->listSupporters($IDTicket);

  	foreach ($ArSupporters1 as $ArField) {
  	  $ArSupporters[$ArField['IDSupporter']] = $ArField['StName'];
  	}

  	foreach ($ArSupporters2 as $ArField) {
  	  $ArSupporters[$ArField['IDSupporter']] = $ArField['StName'];
  	}

    return $ArSupporters;
  }

  /**
   * count how many ticket were not read by a supporter in a department
   *
   * @param int  $IDDepartment
   * @param bool $isSupporter
   *
   * @return int
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public static function notReadCount( $IDUser, $isSupporter = true ) {
  	$ObTicket = self::getInstance( "TicketHandler" );

  	if ($isSupporter) {
  	 $notRead = $ObTicket->notReadCount( $IDUser );
  	 $ArDepartments = F1DeskUtils::getDepartments( $IDUser );
  	 foreach ($ArDepartments as $Key=>$Department) {
  	   if (array_key_exists($Key,$notRead) == false) {
  	     $notRead[$Key]['notRead'] = 0;
  	   }
  	 }
  	} else {
  	 $notRead = $ObTicket->UserNotReadCount( $IDUser );
  	}

  	return $notRead;
  }

  /**
   * Get information about the ticket to make the exibition headers
   *
   * @param int $IDTicket
   * @return Array
   */
  public static function getTicketHeaders($IDTicket) {
    $ObjTicket = self::getInstance('TicketHandler');
    $ArHeaders = $ObjTicket->getTicketHeaders($IDTicket);

    return array_shift($ArHeaders);
  }

  /**
   * Print's all messages of the ticket given
   *
   * @param integer $IDTicket
   */
  public static function getHistory($IDTicket) {
    $i = 0;
    $ObjTicket = self::getInstance('TicketHandler');
    $ArMessages = $ObjTicket->listTicketMessages($IDTicket);
    #
    # for exibition, replaces "\n" for "<br>"
    #
    foreach ($ArMessages as &$ArMessageSettings) {
      $ArMessageSettings['TxMessage'] = nl2br( $ArMessageSettings['TxMessage'] );
    }
    
    foreach ($ArMessages as &$ArMessage) {
      switch ($ArMessage['EnMessageType']) {
        case 'SYSTEM':
          $StClass = 'messageSystem';
        break;
        case 'INTERNAL':
          $StClass = 'messageInternal';
        break;
        default:
          $StClass = 'message';
          if ($i++ % 2 == 0) { $StClass .= 'Alt'; }
        break;
      }

      $ArMessage['StClass'] = $StClass;
    }

    return $ArMessages;
  }

  /**
	 * outputs the right page, handling templates
	 *
	 * @return bool
	 */
	public static function showPage($StPage) {
	  $StPage = preg_replace('/[^A-Z0-9]*/i','',$StPage);
	  if (file_exists(ABSTEMPLATEDIR . $StPage . '.php')) {
	    self::$CurrentPage = $StPage;
	    require_once(ABSTEMPLATEDIR . $StPage . '.php');
	  } else {
	    self::$CurrentPage = 'home';
	    require_once(ABSTEMPLATEDIR . 'home.php');
	  }

	  return true;
	}

	/**
	 * return all Menu Tabs configured on options.xml
	 *
	 * @param string $StPage
	 *
	 * @return array
	 */
	public static function getMenuTab( $StPage ) {
		$ArMenu = array();
		$ObMenu = getOption( "menu_tabs", "DOM" );
		foreach ( $ObMenu as $Item ){
			if ( $StPage == $Item->getAttribute('id') ) $StCurrent = "current";
			else $StCurrent = "";
			$ArMenu[] = array(
				"Link" => $Item->getAttribute('id'),
				"Name" => $Item->nodeValue,
				"Current" => $StCurrent
			);
		}

		return $ArMenu;
	}

	/**
	 * Get all ticket types
	 *
	 * @return Array
	 *
	 * @author Matheus Ashton
	 */
	public static function getTicketTypes() {
	  $ArTypes = F1DeskUtils::listTicketTypes();
	  for($i=0; $i < count($ArTypes); $i++ ) {
      $ArReturn [ $ArTypes[$i]['IDType'] ] = $ArTypes[$i]['StType'];
      if (count($ArReturn <= 0)) {
        $ArReturn[0] = EXC_NOTTYPE;
      }
      return $ArReturn;
    }
	}

	/**
	 * Get's user data from an IDSupporter or an IDClient
	 *
	 * @param unknown_type $ID
	 * @param unknown_type $ItType
	 * @return unknown
	 */
  public static function getUserData( $ID, $ItType ){
		$ArUserData = F1DeskUtils::getUserData( $ID, $ItType );
		return $ArUserData;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $IDUser
	 * @param unknown_type $ArData
	 * @return unknown
	 */
	public static function updateUserData($IDUser, $ArData){
		$ItAffected = F1DeskUtils::updateUserData( $IDUser, $ArData );
		return $ItAffected;
	}

	/**
	 * Get All Canned Responses of a supporter and their departments
	 *
	 * @return array
	 *
	 * @author Matheus Ashton <matheus@digirati.com.br>
	 */
	public static function getCannedResponses($IDSupporter, $IDDepartment = false) {
    $ArResponses = F1DeskUtils::listCannedResponses($IDSupporter, $IDDepartment);
    return $ArResponses;
	}

	/**
	 * Creates a new cannedResponse for a supporter
	 *
	 * @param string $StAlias
	 * @param string $StTitle
	 * @param text $TxMessage
	 *
	 * @return integer $ItAffected
	 */
	public static function createCannedResponse ( $StAlias, $StTitle, $TxMessage, $ItIDSupporter ){
		$ArData = array(
			"StAlias" => $StAlias,
			"StTitle" => $StTitle,
			"TxMessage" => $TxMessage,
			"BoPersonal" => "1",
			"IDSupporter" => $ItIDSupporter
		);
		return F1DeskUtils::createCannedResponse( $ArData );
	}

	/**
	 * edits a Canned Response
	 *
	 * @param unknown_type $IDCannedResponse
	 * @param unknown_type $ArData
	 */
	public static function editCannedResponse( $IDCannedResponse, $ArData ){
		$ItAffected = F1DeskUtils::editCannedResponse( $IDCannedResponse, $ArData );
		if ($ItAffected <= -1) {
			 return false;
		}
		return $ItAffected;
	}

	/**
	 * reova a canned response
	 *
	 * @param unknown_type $IDCannedResponse
	 */
	public static function removeCannedResponse ( $IDCannedResponse ) {
		$ItAffected = F1DeskUtils::removeCannedResponse( $IDCannedResponse );
		if ($ItAffected <= 0) {
			 return false;
		}
		return $ItAffected;
	}

	/**
	 * lists all Notes of a supporter
	 *
	 * @param integer $IDSupporter
	 * @return array
	 */
	public static function listNotes( $IDSupporter ){
		return F1DeskUtils::listNotes( $IDSupporter );
	}

	/**
	 * Creates a new Note for a supporter
	 *
	 * @param string $StTitle
	 * @param text $TxMessage
	 *
	 * @return integer $ItAffected
	 */
	public static function createNote ( $StTitle, $TxMessage, $ItIDSupporter ){
		$ArData = array(
			"StTitle" => $StTitle,
			"TxNote" => $TxMessage,
			"IDSupporter" => $ItIDSupporter
		);
		return F1DeskUtils::createNote( $ArData );
	}

	/**
	 * edits a Note
	 *
	 * @param integer $IDCannedResponse
	 * @param array $ArData
	 */
	public static function editNote( $IDNote, $ArData ){
		$ItAffected = F1DeskUtils::editNote( $IDNote, $ArData );
		if ($ItAffected <= -1) {
			 return false;
		}
		return $ItAffected;
	}

	/**
	 * reova a note
	 *
	 * @param integer $IDNote
	 */
	public static function removeNote ( $IDNote ) {
		$ItAffected = F1DeskUtils::removeNote( $IDNote );
		if ($ItAffected <= 0) {
			 return false;
		}
		return $ItAffected;
	}

	/**
	 * lists all Bookmark of a supporter
	 *
	 * @param integer $IDSupporter
	 * @return array
	 */
	public static function listSupporterBookmark( $IDSupporter ){
		return F1DeskUtils::listSupporterBookmark( $IDSupporter );
	}

	/**
	 * remove a supporter Bookmark
	 *
	 * @param integer $IDTicket
	 */
	public static function removeBookmark ( $IDTicket, $IDSupporter ) {
		$ItAffected = F1DeskUtils::removeBookmark( $IDTicket, $IDSupporter );
		if ($ItAffected <= 0) {
			 return false;
		}
		return $ItAffected;
	}

	/**
	 * Checks if the user is a supporter
	 *
	 * @return unknown
	 */
	public static function IsSupporter() {
	  return (getSessionProp('isSupporter') && getSessionProp('isSupporter') == 'true');
	}

	/**
	 * Get all attachments from all messagens of a call
	 *
	 * @param int $IDTicket
	 * @return array
	 *
	 * @author Matheus Ashton <matheus@digirati.com.br>
	 */
	public static function getAttachments($IDTicket) {
	  $ArAttachments = array();
	  $TicketHandler = self::getInstance('TicketHandler');

	  $ArMessages = $TicketHandler->listTicketMessages($IDTicket);

	  foreach ($ArMessages as $ArMessage) {
	    $ArAttachment = $TicketHandler->getAttachments($ArMessage['IDMessage']);
	    if (! empty($ArAttachment))
	    $ArAttachments[$ArMessage['IDMessage']] = $ArAttachment;
	  }
	  return $ArAttachments;
	}

  /**
   * Return the non-internal departments or all departments
   *
   * @return Array
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
	public static function getPublicDepartments($BoPublic = true) {
	  $TicketHandler = self::getInstance('TicketHandler');
	  $ArPublic = $TicketHandler->getPublicDepartments($BoPublic);
	  return $ArPublic;
	}
	
	/**
	 * get the preview of a wrote answer
	 *
	 * @param integer $IDUser
	 * @param text $TxMessage
	 * @return text
	 */
	public static function getPreviewAnswer($IDUser, $TxMessage) {
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getPreviewAnswer($IDUser, $TxMessage);
	}
}

?>