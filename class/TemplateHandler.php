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
   * get the tickets of all departments
   *
   * @param array $ArIDDepartment
   * @param int $IDSupporter
   * @return array
   */
  public static function getTickets($ArIDDepartment,$IDSupporter) {
    $ArTickets = array();

    $ObjTicket = self::getInstance('TicketHandler');
    $ArDepartmentTickets['open'] = $ObjTicket->listTickets($ArIDDepartment);
    $ArDepartmentTickets['ignored'] = $ObjTicket->listIgnoredTickets($IDSupporter);
    $ArDepartmentTickets['bookmark'] = $ObjTicket->listBookmarkTickets($IDSupporter);
    $ArDepartmentTickets['single'] = $ObjTicket->listSingleTickets($IDSupporter);
    $ArDepartmentTickets['byme'] = $ObjTicket->listByMeTickets($IDSupporter);

    foreach ($ArIDDepartment as $IDDepartment) {
      if (array_key_exists($IDDepartment,$ArDepartmentTickets)) {
        $ArCurrentTickets = $ArDepartmentTickets[$IDDepartment];
      } else {
        if (array_key_exists($IDDepartment,$ArDepartmentTickets['open'])) {
          $ArCurrentTickets = $ArDepartmentTickets['open'][$IDDepartment];
        } else {
          $ArCurrentTickets = array();
        }
      }

      $TicketList = array();
      foreach ($ArCurrentTickets as $ArCurrentTicket) {
        $TicketList[] = $ArCurrentTicket['IDTicket'];
      }

      $ArReadTickets = $ObjTicket->getReadTickets($IDSupporter, $TicketList);

      $ItnotReadCount = 0;

      foreach ($ArCurrentTickets as &$ArTicket) {
        if (array_key_exists($ArTicket['IDTicket'],$ArReadTickets)) {
          $ArTicket['isRead'] = 1;
        } else {
          $ArTicket['isRead'] = 0;
          ++$ItnotReadCount;
        }
      }

      $ArTickets[$IDDepartment]['Tickets'] = $ArCurrentTickets;
      $ArTickets[$IDDepartment]['notReadCount'] = $ItnotReadCount;
    }

    return $ArTickets;
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
  public static function showHistory($IDTicket, $ArAttachments) {
    $i = 0;
    $ObjTicket = self::getInstance('TicketHandler');
    $ArMessages = $ObjTicket->listTicketMessages($IDTicket);
    $StHtml = "";
    #
    # for exibition, replaces "\n" for "<br>"
    #
    foreach ($ArMessages as &$ArMessage) {
      $ArMessage['TxMessage'] = nl2br( $ArMessage['TxMessage'] );

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
      if (!TemplateHandler::IsSupporter() && $ArMessage['EnMessageType'] == 'INTERNAL')
        continue;
      $DtSended = F1DeskUtils::formatDate('datetime_format',$ArMessage['DtSended']);
      $StHtml .= "<div class='{$ArMessage['StClass']}'>";
      $StHtml .= '<b>'.DATE_MSG_SENT.$DtSended.BY.'<span class="TxAtendente">'.$ArMessage['SentBy'].'</span></b>';
      if (array_key_exists($ArMessage['IDMessage'],$ArAttachments)) {
        foreach ($ArAttachments[$ArMessage['IDMessage']] as $Attachment) {
          $StHtml .= "<p><b>".ATTACHMENT."</b>: <a class='Link' href='download.php?IDAttach={$Attachment['IDAttachment']}'>{$Attachment['StFile']}</a></p>";
        }
      }
      $StHtml .= '<p>'.$ArMessage['TxMessage'] . '</p></div>';
    }
    return $StHtml;
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
	public static function createCannedResponse ( $StTitle, $TxMessage, $ItIDSupporter ){
		$ArData = array(
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
	public static function getPreviewAnswer($IDUser, $TxMessage, $BoIsSupporter = false) {
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getPreviewAnswer($IDUser, $TxMessage, $BoIsSupporter);
	}

	/**
   * get all tickets that attached this ticket
   *
   * @param integer $IDTicket
   * @return array
   */
	public static function getTicketsAttached($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketsAttached($IDTicket);
	}

	/**
   * get all attacheds tickets from a ID given
   *
   * @param integer $IDTicket
   * @return array
   */
	public static function getAttachedTickets($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getAttachedTickets($IDTicket);
	}

	/**
	 * get all departments of a ticket given
	 *
	 * @param integer $IDTicket
	 * @return array
	 */
	public static function getTicketDepartments($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketDepartments($IDTicket);
	}

	/**
	 * get all departments Reader of a ticket given
	 *
	 * @param integer $IDTicket
	 * @return array
	 */
	public static function getTicketDepartmentsReader($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketDepartmentsReader($IDTicket);
	}

	/**
   * get who users a ticket was sent to
   *
   * @param integer $IDTicket
   * @return array
   */
	public static function getTicketDestination($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketDestination($IDTicket);
	}

	/**
   * get who users can see a ticket
   *
   * @param integer $IDTicket
   * @return array
   */
	public static function getTicketReaders($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketReaders($IDTicket);
	}

	/**
	 * Create the departments combobox in the ticket creation page
	 *
	 * @param array $ArDepartments
	 */
	public static function createFormattedCombo($ArDepartments, $StID = 'IDRecipient', $StName = 'IDRecipient', $StClass = 'inputCombo') {
	  $StHtml = "<select id='$StID' name='$StName' class='$StClass'>";
    $StHtml .= "<option value='null'>".DEFAULT_OPTION."</option>";
	  foreach ($ArDepartments as $ArDepartment) {
	    if(isset($ArDepartment['SubDepartments'])) {
	      $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
	      $StHtml .= "<optgroup>";
	      foreach ($ArDepartment['SubDepartments'] as $SubDepartments) {
	        $StHtml .= "<option value='{$SubDepartments['IDSub']}'>{$SubDepartments['StSub']}</option>";
	      }
	      $StHtml .= "</optgroup>";
	    } else {
	      $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
	    }
	  }
	  $StHtml .= "</select>";
	  return $StHtml;
	}

	/**
	 * Create the comboboxes of categories and priorities in the create ticket page
	 *
	 * @param array $Array
	 * @param string $StID
	 * @param string $StName
	 * @param string $StClass
	 * @return string
	 */
	public static function createCategory_PriorityCombobox($Array, $StID, $StName, $StClass = 'inputCombo') {
	  $StHtml = "<select id='$StID' name='$StName' class='$StClass'>";
	  foreach ($Array as $Key => $Value) {
      $StHtml .= "<option value='$Key'>$Value</option>";
    }
    $StHtml .= "</select>";
    return $StHtml;
	}

	/**
	 * Create the supporters combobox in ticket headers
	 *
	 * @param int $IDTicket
	 * @param array $ArSupporters
	 * @param array $ArHeaders
	 * @param string $StID
	 * @param string $StClass
	 * @return string
	 */
	public static function createSupportersCombo($IDTicket,$ArSupporters, $ArHeaders, $StID, $StClass, $preview) {
	  $StHtml = (isset($ArHeaders['StName'])) ? $ArHeaders['StName'] : '';
	  if (self::IsSupporter() && !$preview) {
	    $StHtml = "<select id='$StID' onchange='TICKET.setTicketOwner(\"$IDTicket\", this.value)' class='$StClass'>";
	    foreach ( $ArSupporters as $IDSupporter => $StSupporter ) {
	      if ($ArHeaders['IDSupporter'] != $IDSupporter) {
	        $StHtml .= "<option value='$IDSupporter'>$StSupporter</option>";
	      } else {
	        $StHtml .= "<option selected='selected' value='$IDSupporter'>$StSupporter</option>";
	      }
	    }
	    $StHtml .= "</select>";
	  } else {
	    foreach ( $ArSupporters as $IDSupporter => $StSupporter ) {
	      if ($ArHeaders['IDSupporter'] == $IDSupporter) {
	        $StHtml = "<span id='$StID'>$StSupporter</span>";
	      }
	    }
	  }
	  return $StHtml;
	}

	/**
	 * create de Departments combobox in ticket header
	 *
	 * @param unknown_type $ArDepartments
	 * @param unknown_type $IDDepartment
	 * @param unknown_type $IDTicket
	 * @param unknown_type $StID
	 * @param unknown_type $StClass
	 * @return unknown
	 */
	public static function createHeaderDepartmentCombo($ArDepartments, $IDDepartment, $IDTicket, $StID, $StClass = 'inputCombo', $preview) {
	  $StHtml = SINGLE;
    if (self::IsSupporter() && !$preview) {
      $StHtml = "<select id='$StID' class='$StClass' onchange='TICKET.changeDepartment(\"$IDTicket\",this.value)'>";
      foreach ($ArDepartments as $ArDepartment) {
        if(isset($ArDepartment['SubDepartments'])) {
          if ($ArDepartment['IDDepartment'] == $IDDepartment) {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}' selected>{$ArDepartment['StDepartment']}</option>";
          } else {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
          }
          $StHtml .= "<optgroup>";
          foreach ($ArDepartment['SubDepartments'] as $SubDepartments) {
            if ($SubDepartments['IDSub'] == $IDDepartment) {
              $StHtml .= "<option value='{$SubDepartments['IDSub']}' selected>{$SubDepartments['StSub']}</option>";
            } else {
              $StHtml .= "<option value='{$SubDepartments['IDSub']}'>{$SubDepartments['StSub']}</option>";
            }
          }
          $StHtml .= "</optgroup>";
        } else {
          if ($ArDepartment['IDDepartment'] == $IDDepartment) {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}' selected>{$ArDepartment['StDepartment']}</option>";
          } else {
            $StHtml .= "<option value='{$ArDepartment['IDDepartment']}'>{$ArDepartment['StDepartment']}</option>";
          }
        }
      }
      $StHtml .= "</select>";
    } else {
      foreach ($ArDepartments as $ArDepartment) {
      	if ($ArDepartment['IDDepartment'] == $IDDepartment) {
          $StHtml = "<span id='{$StID}'>{$ArDepartment['StDepartment']}</span>";
      	}
      	if(isset($ArDepartment['SubDepartments'])) {
      		foreach ($ArDepartment['SubDepartments'] as $ArSubDepartment) {
      			if ($ArSubDepartment['IDSub'] == $IDDepartment) {
      				$StHtml = "<span id='{$StID}'>{$ArSubDepartment['StSub']}</span>";
      			}
      		}
      	}
      }
    }
    return $StHtml;
	}

	/**
	 * show all attached files
	 *
	 * @param array $ArAttachments
	 * @return unknown
	 */
	public static function showAttachments($ArAttachments) {
	  $StHtml = '';
	  if (count($ArAttachments)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_FILES .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArAttachments as $Attachment) {
        $Attachment = $Attachment[0];
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= "<a class='Link' href='download.php?IDAttach={$Attachment['IDAttachment']}'>{$Attachment['StFile']}</a>";
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
	  }
	  return $StHtml;
	}

	/**
	 * show all attached tickets
	 *
	 * @param unknown_type $ArAttachedTickets
	 * @return unknown
	 */
	public static function showAttachedTickets($ArAttachedTickets) {
	  $StHtml = '';
	  if (count($ArAttachedTickets)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_ATTACHED_TICKETS .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArAttachedTickets as $AttachedTicket) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= "<a class='Link' href='javascript:void(0);' onclick='flowWindow.previewTicket(\"{$AttachedTicket['IDAttachedTicket']}\")'>#{$AttachedTicket['IDAttachedTicket']}</a>";
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all tickets attached
	 *
	 * @param array $ArAttachedTickets
	 * @return string HTML
	 */
	public static function showTicketsAttached($ArTicketsAttached) {
	  $StHtml = '';
	  if (count($ArTicketsAttached)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_TICKETS_ATTACHED .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketsAttached as $TicketAttached) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= "<a class='Link' href='javascript:void(0);' onclick='flowWindow.previewTicket(\"{$TicketAttached['IDTicket']}\")'>#{$TicketAttached['IDTicket']}</a>";
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all recipient departments
	 *
	 * @param unknown_type $ArTicketDepartments
	 * @return unknown
	 */
	public static function showTicketDepartments($ArTicketDepartments) {
	  $StHtml = '';
	  if (count($ArTicketDepartments)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_DEPARTMENT_SENTTO .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketDepartments as $TicketDepartments) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketDepartments['StDepartment'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all recipient supporters
	 *
	 * @param unknown_type $ArTicketDestinations
	 * @return unknown
	 */
	public static function showTicketSupporters($ArTicketDestinations) {
	  $StHtml = '';
	  if (count($ArTicketDestinations)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_SUPPORTER_SENTTO .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketDestinations as $TicketDestination) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketDestination['StName'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all department readers
	 *
	 * @param unknown_type $ArTicketDepartmentsReader
	 * @return unknown
	 */
	public static function showDepartmentReaders($ArTicketDepartmentsReader) {
	  $StHtml = '';
	  if (count($ArTicketDepartmentsReader)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_DEPARTMENTS_READER .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketDepartmentsReader as $TicketDepartmentsReader) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketDepartmentsReader['StDepartment'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
    }
    return $StHtml;
	}

	/**
	 * show all supporter readers
	 *
	 * @param unknown_type $ArTicketReaders
	 * @return unknown
	 */
	public static function showSupporterReaders($ArTicketReaders) {
	  $StHtml = '';
	  if (count($ArTicketReaders)!=0) {
	    $StHtml = '<table class="tableTickets">
	                 <thead><th>'. INFO_SUPPORTER_READER .'</th></thead>';
      $StHtml .= "<tbody><td>";
      $i=0;
      foreach ($ArTicketReaders as $TicketReaders) {
        if($i!=0)
          $StHtml .= ', ';
        $StHtml .= $TicketReaders['StName'];
        $i++;
      }
      $StHtml .= "</td> </tbody> </table>";
	  }
	  return $StHtml;
	}

	/**
	 * Create the combobox with the Canned Answers
	 *
	 * @param unknown_type $ArResponses
	 * @return unknown
	 */
	public static function createCannedCombo($ArResponses) {
	  $StHtml = '';
	  if (self::IsSupporter()) {
	    $StHtml = "<select class='inputCombo' id='cannedAnswers'>";
	    if ($ArResponses[0]['IDCannedResponse'] != '') {
	      foreach ($ArResponses as $Response) {
	        $StHtml .= "<option value='".(f1desk_escape_string($Response['TxMessage'],true,true))."' >".$Response['StTitle']."</option>";
        }
	    } else {
	      $StHtml .= "<option value='null'>".NO_ANSWER."</option>";
      }
      $StHtml .= '</select>';
      $StHtml .= "<button class='button' onclick='TICKET.addCannedResponse(); return false;'>".ADD."</button>";
    }
    return $StHtml;
	}

	/**
	 * return the category of a ticket
	 *
	 * @param integer $IDTicket
	 * @return string
	 */
	public static function getTicketCategory($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketCategory($IDTicket);
	}

	/**
	 * return the priority of a ticket
	 *
	 * @param integer $IDTicket
	 * @return string
	 */
	public static function getTicketPriority($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketPriority($IDTicket);
	}

	/**
	 * return the type of a ticket
	 *
	 * @param integer $IDTicket
	 * @return string
	 */
	public static function getTicketType($IDTicket){
	  $TicketHandler = self::getInstance('TicketHandler');
	  return $TicketHandler->getTicketType($IDTicket);
	}

	/**
	 * Create a combobox with all ticket types registered
	 *
	 * @param unknown_type $StClass
	 * @return unknown
	 */
	public static function showTicketTypes($StClass = 'inputCombo') {
	  $ArTypes = self::getTicketTypes();
	  if (!empty($ArTypes)) {
	    $StHtml = "<select id='IDType' name='IDType' class='$StClass'>";
	    foreach ($ArTypes as $Key => $Type) {
	      $StHtml .= "<option value='$Key'>$Type</option>";
	    }
	    $StHtml .= '</select>';
	  } else {
	    $StHtml = '<span>'.NOTYPE.'</span>';
	  }
    return $StHtml;
	}

	public static function showCannedAnswers($ArCannedResponses, $StClass = 'inputCombo') {
	  $StHtml = '<tr id="noCanned">';
    $StHtml .= '<td colspan="3" align="center">'.NO_CANNED.'</td></tr>';
	  if (!empty($ArCannedResponses[0]['IDCannedResponse'])) {
      foreach ($ArCannedResponses as $ArCannedResponse) {
        $StHtml = "<tr id='cannedTR{$ArCannedResponse['IDCannedResponse']}'>";
        $StHtml .= '<td class="TicketNumber">';
        $StHtml .= $ArCannedResponse['StTitle'];
        $StHtml .= "<input type='hidden' id='StCannedTitle{$ArCannedResponse['IDCannedResponse']}' value='".f1desk_escape_string($ArCannedResponse['StTitle'],false,true)."'>";
        $StHtml .= '</td><td>';
        $StHtml .= "<input type='hidden' id='TxCannedResponse{$ArCannedResponse['IDCannedResponse']}' value='".f1desk_escape_string($ArCannedResponse['TxMessage'],false,true)."'>";
        $StHtml .= "<img src='".TEMPLATEDIR."images/button_edit.png' alt='Editar' title='Editar' class='cannedAction' onclick='startEditElement(\"canned\",\"{$ArCannedResponsesSettings['IDCannedResponse']}\");'>";
        $StHtml .= "<img src='".TEMPLATEDIR."images/button_cancel.png' alt='Remover' title='Remover' class='cannedAction' onclick='removeCannedResponse(\"{$ArCannedResponsesSettings['IDCannedResponse']}\")'>";
        $StHtml .= "<img src='".TEMPLATEDIR."images/visualizar.png' title='Visualizar' id='previemCanned{$ArCannedResponsesSettings['IDCannedResponse']}' alt='Visualizar' class='cannedAction' onclick='previewInFlow.CannedResponse(\"".f1desk_escape_string($ArCannedResponsesSettings['StTitle'],false,true)."\",".f1desk_escape_string($ArCannedResponsesSettings['TxMessage'], true,true)."\");'>";
        $StHtml .= '</td> </tr>';
      }
    }
    return $StHtml;
	}
}
?>