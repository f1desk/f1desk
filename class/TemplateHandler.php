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
  public static function getDepartments( $IDUser, $Format = true ) {
    $ArReturn = array();
    list($ArDepartments,$ArSubDepartments) = F1DeskUtils::listDepartments( $IDUser );

    foreach ($ArSubDepartments as $ArQuery) {
    	$IDs = explode(',', $ArQuery['IDSubDepartments']);

    	#
    	# setting the "StNome"
    	#
    	$ArReturn[ $ArQuery['IDDepartment'] ] = array
    	(
    		"IDDepartment" => $ArQuery['IDDepartment'],
				"StName" => $ArDepartments[ $ArQuery['IDDepartment'] ],
			);


			#
			# setting the IDs of any SubDeptos (formatted or not)
			#
			if ($Format == true) {
  			$ArReturn[ $ArQuery['IDDepartment'] ][ 'SubDepartment' ] = array();
  			foreach ( $IDs as $id ){
  				if ( trim($id) != "" ) {
  					$ArReturn[ $ArQuery['IDDepartment'] ][ 'SubDepartment' ][ $id ] = array(
  						 "IDSubDepartment" => $id,
  						 "StName"  => $ArDepartments[$id]
  					);
  					unset( $ArDepartments[$id] );
  				}
  			}
			} else {
			  foreach ( $IDs as $id ){
  				if ( trim($id) != "" ) {
  					$ArReturn[ $id ] = array(
  						 "IDDepartment" => $id,
  						 "StName"  => $ArDepartments[ $ArQuery['IDDepartment'] ] . ' :: ' . $ArDepartments[$id]
  					);
  					unset( $ArDepartments[$id] );
  				}
  			}
			}
			unset( $ArDepartments[ $ArQuery['IDDepartment'] ] );
    }

    #
    # getting departments whitout subdepartments
    #
    foreach ($ArDepartments as $IntIDDepto => $StDepto) {
    	$ArReturn[ $IntIDDepto ] = array
    	(
    		"IDDepartment" => $IntIDDepto,
    		"StName" => $StDepto,
    		"SubDepartment" => array()
    	);
    }

    return $ArReturn;
  }

  /**
   * List all tickets with the opened situation by a department given
   *
   * @param integer $IDDepartment
   * @return array
   */
  public static function listTickets( $IDDepartment, $IDSupporter ){

  	$ObTicket = self::getInstance( "TicketHandler" );
  	$opendTickets = $ObTicket->listTickets( $IDDepartment, $IDSupporter );

  	return $opendTickets;
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
  	$opendTickets = $ObTicket->listClientTickets( $IDUser, $BoOpened );

  	return $opendTickets;

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
   * Check if a Ticket was read by a supporter
   *
   * @param integer $IDSupporter
   * @param integer $IDTicket
   *
   * @return boolean
   */
  public static function isTicketRead( $IDSupporter, $IDTicket ) {

  	$ObTicket = self::getInstance( "TicketHandler" );
  	$BoRead = $ObTicket->isRead( $IDSupporter, $IDTicket );
  	#
  	# If this ticket was read by this supporter, we'll find them on isRead table
  	#
  	if ( $BoRead[0]['Total'] == 0 ) {
  		return false;
  	} else {
  		return true;
  	}

  }

  /**
   * count how many ticket were not read by a supporter in a department
   *
   * @param integer $IDDepartment
   * @param integer $IDSupporter
   * @param strign  $StUserType -> 'client' or 'supporter'
   *
   * @return integer
   */
   public static function notReadCount( $IDDepartment, $IDUser, $StUserType ) {
  	$ObTicket = self::getInstance( "TicketHandler" );
  	$ArReadCount = $ObTicket->notReadCount( $IDDepartment, $IDUser, $StUserType );
  	if ( $ArReadCount['returnType'] == "supporter" ) {
  		list( $ItOpened, $ItRead ) = $ArReadCount['returnContent'];
  		$ItNotReadCount = $ItOpened - $ItRead;
	  	if ( $ItNotReadCount < 0 ) {
	  		return 0;
	  	} else {
	  		return $ItNotReadCount;
	  	}
  	} else {
  		if ( count( $ArReadCount['returnContent'][ 'opened' ] ) < 0 ) {
  			$ArReadCount['returnContent'][ 'opened' ] = 0;
  		}
  		if ( count( $ArReadCount['returnContent'][ 'closed' ] ) < 0 ) {
  			$ArReadCount['returnContent'][ 'closed' ] = 0;
  		}
  		return $ArReadCount['returnContent'];
  	}
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
}

?>