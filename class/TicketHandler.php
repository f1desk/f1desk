<?php

/**
 *  Class to handle the calls
 *
 */
class TicketHandler extends DBHandler {

  /**
   * Construct \ o /
   *
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * this functions handles the checking and the upload to db or ftp
   * of the attachment
   *
   * @param Array $Files
   * @param Int $IDMessage
   * @return boolean
   */
  public function attachFile( $Files, $IDMessage ) {

    if (count($Files) == 1) {
      $StField = key($Files);


      # get file information
      $StFile = $Files[$StField]['name'];
      $StTmp = $Files[$StField]['tmp_name'];
      $ItSize = $Files[$StField]['size'];


      # checking if file is valid
      if ( ! is_uploaded_file($StTmp)) {
        throw new ErrorHandler(EXC_CALL_NOTUPLOADFILE);
      }

      if ($ItSize > UPLOAD_MAX_SIZE) {
        throw new ErrorHandler(EXC_CALL_MAXFILESIZE);
      }

      if ( ! $StFile = $this->_validateFile($StFile) ) {
        throw new ErrorHandler(EXC_CALL_INVALIDTYPE);
      }

      # checking if upload its to db or ftp
      if (UPLOAD_OPT == 'DB') {

        # inserting file content on DB
        $ByFile = file_get_contents($StTmp);
        $ByFile = addslashes($ByFile);

        $StSQL = "
INSERT INTO
  " . DBPREFIX . "Attachment
SET
  StFile = '$StFile',
  ByFile = '$ByFile',
  IDMessage = $IDMessage";

      } else {

        # uploading file to ftp

        $StUploadedFile = UPLOADDIR . $this->_generateFileName();
        if (! move_uploaded_file($StTmp,$StUploadedFile) ) {
          throw new ErrorHandler(EXC_CALL_NOTUPLOADFILE);
        }
        $StSQL = "
INSERT INTO
  " . DBPREFIX . "Attachment
SET
  StFile = '$StFile',
  StLink = '$StUploadedFile',
  IDMessage = $IDMessage";

      }

      $this->setQuery($StSQL);
      $this->commit();
    }

    return true;
  }

  /**
   * checking if the file has an illegal extension and translating illegal chars
   *
   * @param String $StFile
   * @return string/boolean
   */
  private function _validateFile( $StFile ) {
    $ArSearch = array("Á"=>"A","À"=>"A","Ã"=>"A","Â"=>"A",
                "á"=>"a","à"=>"a","ã"=>"a","â"=>"a",
                "É"=>"E","Ê"=>"E",
                "é"=>"e","ê"=>"e",
                "Í"=>"I",
                "í"=>"i",
                "Ó"=>"O","Õ"=>"O","Ô"=>"O",
                "ó"=>"o","õ"=>"o","ô"=>"o",
                "Ú"=>"U",
                "ú"=>"u",
                "Ç"=>"C","ç"=>"c",
                "?"=>"_","#"=>"_","$"=>"_","<"=>"_",">"=>"_","%"=>"_","&"=>"_","@"=>"_","¬"=>"_");

    $StFile = strtr( $StFile, $ArSearch );
    $ArInvalidEXT = array('exe','bin','sh','cmd','ceo','bat','pif','com','scr','vbs','vbe','reg','jse','lnk','mhtml','asp');

    $ItLast = strrpos( $StFile,'.' );
    $StExt = substr( $StFile,($ItLast+1) );

    if ( in_array($StExt,$ArInvalidEXT) === false ) {
      return $StFile;
    } else {
      return false;
    }

  }

  /**
   * generating a random filename
   *
   * @return string
   */
  private function _generateFileName() {

    $ItNumero = rand(0,1);
    $StFileName = '';

    for ($i=1;$i<=20;$i++) {
      if ($ItNumero % 2 == 0) {
        $ItNumero = mt_rand(65,90);
      } else {
        $ItNumero = mt_rand(97,122);
      }

      $StFileName .= chr($ItNumero);
    }

    $StFileName .= '.f1';
    return $StFileName;

  }

  /**
   * list the calls related to an especific department
   *
   * @param array $ArIDDepartment
   *
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _listTickets($ArIDDepartment){

  	if ( is_null( $ArIDDepartment ) ) {
  		throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
  	}

  	$ArNotAllowed = array('single','bookmark','ignored','mine');
  	if (is_array($ArIDDepartment)) {
  	  foreach ($ArIDDepartment as $Key => $IDDepartment) {
  	    if (in_array($IDDepartment,$ArNotAllowed) ) {
  	      unset($ArIDDepartment[$Key]);
  	    }
  	  }
  	  $IDDepartment = implode(', ', $ArIDDepartment);
  	} else {
  	  $IDDepartment = $ArIDDepartment;
  	}

  	$IDDepartment = empty($IDDepartment) ? "''" : $IDDepartment;


		$StSQL = "
SELECT
  T.*,
  D.*,
  U.Stname as StSupporter
FROM
  " . DBPREFIX . "User U
  LEFT JOIN " . DBPREFIX . "Supporter S ON (S.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T ON(T.IDSupporter = S.IDSupporter)
  LEFT JOIN " . DBPREFIX . "TicketDepartment TD ON(TD.IDTicket = T.IDTicket)
  LEFT JOIN " . DBPREFIX . "Department D ON(D.IDDepartment = TD.IDDepartment)
WHERE
  D.IDDepartment IN ($IDDepartment)
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')";

		$this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

		$ArResult = array();
		foreach ($ArTickets as $ArTicket) {
		  $ArResult[$ArTicket['IDDepartment']][] = $ArTicket;
		}

		return $ArResult;
  }

  /**
   * list the ignored tickets
   *
   * @param int $IDSupporter
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _listIgnoredTickets($IDSupporter) {
    $StSQL = "
SELECT
  T.*, U.StName as StSupporter
FROM
  " . DBPREFIX . "User U
  LEFT JOIN " . DBPREFIX . "Supporter S2 ON (S2.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDSupporter = S2.IDSupporter)
  LEFT JOIN " . DBPREFIX . "Ignored I ON (I.IDTicket = T.IDTicket)
  LEFT JOIN " . DBPREFIX . "Supporter S ON (S.IDSupporter = I.IDSupporter)
WHERE
  I.IDSupporter = $IDSupporter
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')";

    $this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

		return $ArTickets;
  }

  /**
   * list the bookmarked tickets
   *
   * @param int $IDSupporter
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _listBookmarkTickets($IDSupporter) {
    $StSQL = "
SELECT
  T.*,  U.StName as StSupporter
FROM
  " . DBPREFIX . "User U
  LEFT JOIN " . DBPREFIX . "Supporter S2 ON (S2.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDSupporter = S2.IDSupporter)
  LEFT JOIN " . DBPREFIX . "Bookmark B ON (B.IDTicket = T.IDTicket)
  LEFT JOIN " . DBPREFIX . "Supporter S ON (S.IDSupporter = B.IDSupporter)
WHERE
  S.IDSupporter = $IDSupporter";

    $this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

		return $ArTickets;
  }

  /**
   * list the Single tickets
   *
   * @param int $IDSupporter
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _listSingleTickets($IDSupporter) {
    $StSQL = "
SELECT
  T.*,  U.StName as StSupporter
FROM
  " . DBPREFIX . "User U
  LEFT JOIN " . DBPREFIX . "Supporter S2 ON (S2.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDSupporter = S2.IDSupporter)
  LEFT JOIN " . DBPREFIX . "TicketSupporter TS ON (TS.IDTicket = T.IDTicket)
  LEFT JOIN " . DBPREFIX . "Supporter S ON (S.IDSupporter = TS.IDSupporter)
WHERE
  S.IDSupporter = $IDSupporter
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')";

    $this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

		return $ArTickets;
  }

  /**
   * list the byme tickets
   *
   * @param int $IDSupporter
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _listByMeTickets($IDSupporter) {
    $StSQL = "
SELECT
  T.*,  U2.StName as StSupporter
FROM
  " . DBPREFIX . "Supporter S
  LEFT JOIN " . DBPREFIX . "User U ON (U.IDUser = S.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Supporter S2 ON (S2.IDSupporter = T.IDSupporter)
  LEFT JOIN " . DBPREFIX . "User U2 ON (U2.IDUser = S2.IDUser)
WHERE
  S.IDSupporter = $IDSupporter
AND
  NOT ISNULL(T.IDTicket)";

    $this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");
		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

		return $ArTickets;
  }

  /**
   * list the user opened tickets
   *
   * @param int $IDUser
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _listUserOpenTickets($IDUser) {
      	$StSQL = "
SELECT
  T.*, U2.StName as StSupporter
FROM
  " . DBPREFIX . "Client C
  LEFT JOIN " . DBPREFIX . "User U on (C.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T on (T.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Supporter S on (S.IDSupporter = T.IDSupporter)
  LEFT JOIN " . DBPREFIX . "User U2 on (U2.IDUser = S.IDUser)
WHERE
  T.StSituation != 'CLOSED'
AND
  U.IDUser = $IDUser";

    $this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");
		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

		return $ArTickets;
  }

  /**
   * list the user closed tickets
   *
   * @param int $IDUser
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _listUserCloseTickets($IDUser) {
      	$StSQL = "
SELECT
  T.*, U2.StName as StSupporter
FROM
  " . DBPREFIX . "Client C
  LEFT JOIN " . DBPREFIX . "User U on (C.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T on (T.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Supporter S on (S.IDSupporter = T.IDSupporter)
  LEFT JOIN " . DBPREFIX . "User U2 on (U2.IDUser = S.IDUser)
WHERE
  T.StSituation = 'CLOSED'
AND
  U.IDUser = $IDUser";

    $this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");
		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

		return $ArTickets;
  }

  /**
   * get the tickets read by a supporter
   *
   * @param integer $IDSupporter
   * @param array   $TicketList
   *
   * @return array
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _getReadTickets($IDSupporter, $TicketList) {

    if (is_array($TicketList)) {
      $Tickets = implode(', ',$TicketList);
      $Tickets = empty($Tickets) ? "''" : $Tickets;
    } else {
      $Tickets = $TicketList;
    }

    $StSQL = "
SELECT
	T.IDTicket
FROM
	". DBPREFIX ."Ticket T
	LEFT JOIN ". DBPREFIX ."isRead R ON (T.IDTicket = R.IDTicket)
	LEFT JOIN ". DBPREFIX ."User U ON (U.IDUser = R.IDUser)
	LEFT JOIN ". DBPREFIX ."Supporter S ON (S.IDUSer = U.IDUSer)
WHERE
  T.IDTicket IN ($Tickets)
AND
  S.IDSupporter = $IDSupporter";

  	$this->execSQL($StSQL);
  	$ArResult = $this->getResult("string");

  	$ArRead = F1DeskUtils::sortByID($ArResult, 'IDTicket');

  	return $ArRead;

  }

  /**
   * get the tickets read by a user
   *
   * @param integer $IDUser
   *
   * @return array
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  private function _getUserReadTickets($IDUser) {

  	$StSQL = "
SELECT
	T.IDTicket
FROM
	". DBPREFIX ."Ticket T
	LEFT JOIN ". DBPREFIX ."isRead R ON (T.IDTicket = R.IDTicket)
	LEFT JOIN ". DBPREFIX ."User U ON (U.IDUser = R.IDUser)
WHERE
  U.IDUser = $IDUser
AND
  T.StSituation != 'CLOSED'";

  	$this->execSQL($StSQL);
  	$ArRead = $this->getResult("string");
  	$ArRead = F1DeskUtils::sortByID($ArRead, 'IDTicket');

  	return $ArRead;

  }

  /**
   * get the tickets of all departments
   *
   * @param array $ArIDDepartment
   * @param int $IDSupporter
   * @return array
   */
  public function getTickets($ArIDDepartment,$IDSupporter) {
    $ArTickets = array();

    $ArDepartmentTickets['open'] = $this->_listTickets($ArIDDepartment);
    $ArDepartmentTickets['ignored'] = $this->_listIgnoredTickets($IDSupporter);
    $ArDepartmentTickets['bookmark'] = $this->_listBookmarkTickets($IDSupporter);
    $ArDepartmentTickets['single'] = $this->_listSingleTickets($IDSupporter);
    $ArDepartmentTickets['byme'] = $this->_listByMeTickets($IDSupporter);
    $ArIgnored = array_keys($ArDepartmentTickets['ignored']);

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
      foreach ($ArCurrentTickets as $Key => $ArCurrentTicket) {
        if ($IDDepartment == 'ignored' || ! in_array($ArCurrentTicket['IDTicket'],$ArIgnored) ) {
          $TicketList[] = $ArCurrentTicket['IDTicket'];
        } else {
          unset($ArCurrentTickets[$Key]);
        }
      }

      $ArReadTickets = $this->_getReadTickets($IDSupporter, $TicketList);

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
   * list the tickets of a client
   *
   * @param int $IDUser
   * @param bool $BoOpened
   *
   * @return array
   *
   * @author Mario Vitor <mario@digirati.com.br>
   */
  public function getUserTickets($IDUser){

  	if ( is_null( $IDUser ) ) {
  		throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
  	}

  	$ArTickets['opened']['Tickets'] = $this->_listUserOpenTickets($IDUser);
  	$ArTickets['closed']['Tickets'] = $this->_listUserCloseTickets($IDUser);
  	$ArReadTickets = array_keys($this->_getUserReadTickets($IDUser));

  	$ArTickets['opened']['notReadCount'] = 0;
    $ArTickets['closed']['notReadCount'] = 0;

  	foreach ($ArTickets['opened']['Tickets'] as &$ArTicket) {
      if (in_array($ArTicket['IDTicket'],$ArReadTickets)) {
        $ArTicket['isRead'] = 1;
      } else {
        $ArTicket['isRead'] = 0;
        ++$ArTickets['opened']['notReadCount'];
      }
  	}

		return $ArTickets;

  }

  /**
   * Set a Call as read for a certain supporter
   *
   * @param integer $ItIDUser
   * @param integer $ItIDCall
   *
   * @return array
   * @author Mario Vítor <mario@digirati.com.br>
   */
  public function setAsRead($IDUser, $IDTicket) {

    if ( empty($IDUser) || empty($IDTicket) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDREADID);
    }

    $StSQL = "
SELECT
  IDTicket
FROM
  ". DBPREFIX ."isRead
WHERE
  IDTicket = $IDTicket
AND
  IDUser = $IDUser";

    $this->execSQL($StSQL);
  	$Qtd = $this->getNumRows();

  	if ($Qtd == 0) {
      #
      # array -> fields of table Read
      #
      $ArFields = array(  'IDUser', 'IDTicket'  );

      #
      # array -> fields of inserting
  		# ( $this->insertIntoTable function needs an array of arrays to insert )
      #
      $ArInsert = array(  array(  $IDUser, $IDTicket  )  );

      $StTableName = DBPREFIX . 'isRead';

      $ItAffected = $this->insertIntoTable($StTableName, $ArFields, $ArInsert);

      return true;
  	}

    return false;

  }

  /**
   * Set a Call as ignored for a certain user
   *
   * @param integer $ItIDUser
   * @param integer $ItIDCall
   *
   * @return array
   * @author Mario Vítor <mario@digirati.com.br>
   */
  public function ignoreTicket($IDSupporter, $IDTicket){

    if ( empty($IDSupporter) || empty($IDTicket) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDIGNOREID);
    }

    # Table Ignored's Fields
    $ArFields = array(  'IDSupporter', 'IDTicket'  );

    $ArInsert = array(  array(  $IDSupporter, $IDTicket  )  );
    $StTableName = DBPREFIX . 'Ignored';
    $ItAffected = $this->insertIntoTable($StTableName, $ArFields, $ArInsert);

    return (!$ItAffected) ? false : true;

  }

  /**
   * Bookmarks a ticket
   *
   * @param int $IDSupporter
   * @param int $IDTicket
   * @return boolean
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public function bookmarkTicket($IDSupporter, $IDTicket) {
    $ArFields = array('IDSupporter', 'IDTicket');
    $ArValues = array($IDSupporter,$IDTicket);
    $StTableName = DBPREFIX . 'Bookmark';
    $ItAffected = $this->insertIntoTable($StTableName,$ArFields,$ArValues);

    return ($ItAffected < 0) ? false : true;
  }

  /**
   * removes a bookmarked ticket
   *
   * @param int $IDSupporter
   * @param int $IDTicket
   * 
   * @return boolean
   */
  public function removeBookmark( $IDSupporter, $IDTicket ) {
		$StTableName = DBPREFIX . 'Bookmark';
		$StCondition = 'IDTicket = ' . $IDTicket . ' AND IDSupporter = ' . $IDSupporter;
    $ItAffected = $this->deleteFromTable($StTableName,$StCondition, 1);

 		return ($ItAffected < 0) ? false : true;
  }

  /**
   * Update a ticket inserting it's new avaliation
   *
   * @param integer $ItIDCall
   * @param integer $ItIDRate
   * @param text $TxRateComment
   *
   * @return array
   * @author Mario Vítor <mario@digirati.com.br>
   */
  public function avaliateTicket($ItIDTicket, $ItIDRate, $TxRateComment = ""){

    if ( empty($ItIDTicket) || empty($ItIDRate) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDAVALIATEID);
    }

    #
    # array -> data to update the table Call
    #
    $ArData = array( 'IDRate' => $ItIDRate, 'TxRateComment' => $TxRateComment );

    $StTableName = DBPREFIX . 'Ticket';

    $StCondition = 'IDTicket = ' . $ItIDTicket;

    $ItAffected = $this->updateTable($StTableName, $ArData, $StCondition);

    return array( (!$ItAffected)?'error':'sucess' => array(  $StTableName, $ArData, $StCondition  ) );

  }

  /**
   * Update database closing a Ticket
   *
   * @param integer $ItIDCall
   *
   * @return array
   *
   * @author Mario Vítor <mario@digirati.com.br>
   */
  public function closeTicket($ItIDTicket){

    if ( empty( $ItIDTicket ) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDCLOSECALLID);
    }

    #
    # array -> data to update table Call
    #
    $ArData = array( 'DtUpdated' => date('Y:m:d H:i:s'), 'StSituation' => 'CLOSED' );

    $StTableName = DBPREFIX . 'Ticket';

    $StCondition = 'IDTicket = ' . $ItIDCall;

    $ItAffected = $this->updateTable($StTableName, $ArData, $StCondition);

    return array( (!$ItAffected)?'error':'sucess' => array(  $StTableName, $ArData, $StCondition  ) );

  }

  /**
   * Adds a new reply to the call
   *
   * @param  int  $IDCall
   * @param  int  $IDUser
   * @param  str  $StMessage
   * @param  boo  $BoAvailable   #if the message needs permission to be shown or not
   * @param  int  $ItMsgType
   *
   * @return int $IDMessage
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public function addMessage($IDUser, $IDTicket, $StMessage, $BoAvailable, $ItMsgType = 0) {

    # message types availables
    $ArTypes = array( 'NORMAL' , 'INTERNAL' , 'SYSTEM', 'SATISFACTION');

    $StMsgType = ($ItMsgType != 4) ? $ArTypes[$ItMsgType] : $ArTypes[0];

    #
    # Add Headers and sign only to normal replies
    #
    if ($ItMsgType == 0) {
      $ArHeaderSign = F1DeskUtils::getUserHeaderSign($IDUser);
      if (!empty($ArHeaderSign['TxHeader'])) {
        $ArHeaderSign['TxHeader'] .= "\n\n";
      }
      if (!empty($ArHeaderSign['TxSign'])) {
        $ArHeaderSign['TxSign'] = "\n\n" . $ArHeaderSign['TxSign'];
      }
      $StMessage = f1desk_escape_string($ArHeaderSign['TxHeader']) . $StMessage . f1desk_escape_string($ArHeaderSign['TxSign']);
      $this->_sendNotifyMessage($IDTicket);
    }

    # preparing to insert on Message table
    $StTableName = DBPREFIX . 'Message';
    $ArFields = array( 'TxMessage' , 'DtSended' , 'BoAvailable' , 'EnMessageType' , 'IDTicket' , 'IDUser' );
    $ArValues = array( $StMessage , date('Y-m-d H:i:s',time()) , $BoAvailable, $StMsgType, $IDTicket, $IDUser );

    $this->insertIntoTable($StTableName,$ArFields,$ArValues);
    $IDMessage = $this->getID();

    return $IDMessage;
  }

  /**
   * Open a ticket created by a supporter
   *
   * @param  int  $IDSupporter
   * @param  str  $StTitle
   * @param  int  $IDRate
   * @param  int  $IDCategory
   * @param  int  $IDPriority
   * @param  txt  $StMessage
   * @param  arr  $ArUsers
   * @param  int  $IDDepartment
   * @param  boo  $BoInternal
   * @param  arr  $ArFiles
   * @return boo  true
   *
   * @author Matheus Ashton <matheus[at]digirati.com.br>
   */
  public function createSupporterTicket ($IDSupporter, $IDCategory, $IDPriority, $StTitle, $StMessage, $IDDepartment = '', $IDReader = '', $ArUsers = array(), $ArReaders = array(), $BoInternal = false, $ArFiles = array()) {

    if (empty($ArUsers) && $IDDepartment == '') {
      throw new ErrorHandler(EXC_GLOBAL_EXPPARAM . 'aaaaa');
    }

    #
    # table name, fields and values to insert
    #
    $StTableName = DBPREFIX . 'Ticket';
    $ArFields = array(
                      'StTitle',
                      'DtOpened',
                      'DtUpdated',
                      'StSituation',
                      'BoRead',
                      'IDSupporter',
                      'IDCategory',
                      'IDPriority',
                      'BoInternal'
                     );

    $ArValues = array(
                      $StTitle,
                      date('Y-m-d',time()),
                      date('Y-m-d',time()),
                      'NOT_READ',
                      '1',
                      $IDSupporter,
                      $IDCategory,
                      $IDPriority,
                      $BoInternal
                     );
    $this->insertIntoTable($StTableName, $ArFields, $ArValues);
    $IDTicket = $this->getID();

    if(!empty($ArUsers)) {
      $StTableName = DBPREFIX . 'TicketSupporter';
      $ArFields = array('IDTicket','IDSupporter');
      foreach ($ArUsers as $IDUser) {
        $ArValue[] = array($IDTicket,$IDUser);
      }
      $this->insertIntoTable($StTableName,$ArFields,$ArValue);
    }

    if (!empty($ArReaders)) {
      $ArValue = array();
      $StTableName = DBPREFIX . 'TicketSupporter';
      $ArFields = array('IDTicket','IDSupporter','BoReader');
      foreach ($ArReaders as $IDUser) {
        $ArValue[] = array($IDTicket,$IDUser,'1');
      }
      $this->insertIntoTable($StTableName,$ArFields,$ArValue);
    }

    if ($IDDepartment != '') {
      $this->insertIntoTable( DBPREFIX . 'TicketDepartment',
                              array('IDTicket','IDDepartment'),
                              array($IDTicket, $IDDepartment));
    }

    if ($IDReader != '') {
      $this->insertIntoTable( DBPREFIX . 'TicketDepartment',
                              array('IDTicket','IDDepartment','BoReader'),
                              array($IDTicket, $IDReader,'1'));
    }

    $StMsgType = ($BoInternal == true) ? 1 : 0;

    $IDUser = array_shift(F1DeskUtils::getUserData($IDSupporter));

    $IDMessage = $this->addMessage($IDUser, $IDTicket, $StMessage,$StMsgType);

    if (! empty($ArFiles)) {
      $this->attachFile($ArFiles,$IDMessage);
    }

    return $IDTicket;
  }

  /**
   * Open a ticket created by a user
   *
   * @param  int  $IDSupporter
   * @param  str  $StTitle
   * @param  int  $IDRate
   * @param  int  $IDCategory
   * @param  int  $IDPriority
   * @param  txt  $StMessage
   * @param  int  $IDDepartment
   * @param  arr  $ArFiles
   * @return boo  true
   *
   * @author Matheus Ashton <matheus[at]digirati.com.br>
   */
  public function createUserTicket ($IDClient, $IDCategory, $IDPriority, $StTitle, $StMessage, $IDDepartment,$ArFiles = array()) {

    $IDUser = array_shift(F1DeskUtils::getUserData($IDClient,1));
    $StTableName = DBPREFIX . 'Ticket';
    $ArFields = array(
                      'StTitle',
                      'DtOpened',
                      'DtClosed',
                      'DtUpdated',
                      'StSituation',
                      'BoRead',
                      'IDUser',
                      'IDCategory',
                      'IDPriority'
                     );

    $ArValues = array(
                      $StTitle,
                      date('Y-m-d',time()),
                      '0000-00-00',
                      date('Y-m-d',time()),
                      'NOT_READ',
                      '1',
                      $IDUser,
                      $IDCategory,
                      $IDPriority
                     );
    $itReturn = $this->insertIntoTable($StTableName, $ArFields, $ArValues);
    $IDTicket = $this->getID();

    $IDMessage = $this->addMessage($IDUser, $IDTicket, $StMessage,0);
    if (!empty($ArFiles)) {
      $this->attachFile($ArFiles,$IDMessage);
    }

    $StTableName = DBPREFIX . "TicketDepartment";
    $ArFields = array('IDTicket','IDDepartment');
    $ArValues = array($IDTicket,$IDDepartment);
    $itReturn = $this->insertIntoTable($StTableName, $ArFields, $ArValues);

    return $IDTicket;
  }

  /**
   * Attach a call on an other
   *
   * @param integer $ItIDCall
   * @param integer $ItIDAttachedCall
   *
   * @return array
   *
   * @author Mario Vítor <mario@digirati.com.br>
   */
  public function attachTicket($IDTicket, $IDAttachedTicket) {

    if ( empty($IDAttachedTicket) || empty($IDTicket) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDATTACHID);
    }

    $StTableName = DBPREFIX . 'AttachedTicket';

    $ArFields = array(  'IDTicket', 'IDAttachedTicket'  );

    $ArInsert = array(  $IDTicket, $IDAttachedTicket  );

    $ItAffected = $this->insertIntoTable($StTableName, $ArFields, $ArInsert);

    return ($ItAffected) ? true : false;

  }

  /**
   * Creates a reply to Ticket given
   *
   * @param int $IDWriter
   * @param int $IDTicket
   * @param str $TxMessage
   * @param str $StMsgType
   * @param arr $ArFiles
   * @return boolean
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public function answerTicket ($IDWriter, $IDTicket, $TxMessage, $StMsgType, $ArFiles = array()) {
    #check if the answer came from a supporter or a client
    if ( F1DeskUtils::IsSupporter() ) {
      $this->_supporterAnswer($IDWriter,$IDTicket,$TxMessage, $StMsgType, $ArFiles);
    } else {
      $this->_clientAnswer($IDWriter,$IDTicket,$TxMessage, $ArFiles);
    }

    #Setting Ticket as not read
    $StTableName = DBPREFIX . 'isRead';
    $StCondition = "IDTicket = $IDTicket";
    $this->deleteFromTable($StTableName, $StCondition);

    return true;
  }

  /**
   * Supporters Reply
   *
   * @param int $IDWriter
   * @param int $IDTicket
   * @param str $TxMessage
   * @param str $StMsgType
   * @param arr $ArFiles
   * @return boolean
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  private function _supporterAnswer($IDWriter,$IDTicket,$TxMessage, $StMsgType, $ArFiles = array()) {
    #check if message can be released
    $BoReleased = F1DeskUtils::getPermission('BoReleaseAnswer',$IDWriter);

    #Get table's User ID
    $IDUser = array_shift(F1DeskUtils::getUserData($IDWriter));

    #Add the reply
    $this->addMessage($IDUser, $IDTicket, $TxMessage, $BoReleased, $StMsgType);
    $IDMessage = $this->getID();
    if (!empty($ArFiles)) {
      $this->attachFile($ArFiles,$IDMessage);
    }

    if ($StMsgType == '0') {
      #Changing Tickets's situation
      $StTableName = DBPREFIX . 'Ticket';
      $ArFields = array('StSituation' => 'WAITING_USER', 'IDSupporter' => $IDWriter);
      $this->updateTable($StTableName,$ArFields,"IDTicket = $IDTicket");
    }


    return true;
  }

  /**
   * Clients Reply
   *
   * @param int $IDWriter
   * @param int $IDTicket
   * @param str $TxMessage
   * @param str $ArFiles
   * @return boolean
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  private function _clientAnswer($IDWriter,$IDTicket,$TxMessage, $ArFiles = array()) {

    #Get table's User ID
    $IDUser = array_shift(F1DeskUtils::getUserData($IDWriter,1));

    #Add the reply
    $this->addMessage($IDUser, $IDTicket, $TxMessage, true, 0);
    $IDMessage = $this->getID();
    if (!empty($ArFiles)) {
      $this->attachFile($ArFiles,$IDMessage);
    }

    #Changing Tickets's situation
    $StTableName = DBPREFIX . 'Ticket';
    $ArFields = array('StSituation' => 'WAITING_SUP');
    $this->updateTable($StTableName,$ArFields,"IDTicket = $IDTicket");

    return true;
  }

  /**
   * Get information about the ticket to make the exibition headers
   *
   * @param int $IDTicket
   */
  public function getTicketHeaders($IDTicket) {

    $StSQL = "
SELECT
  T.*, D.*, U.StName
FROM
  " . DBPREFIX . "Ticket T
  LEFT JOIN " . DBPREFIX . "TicketDepartment TD ON(T.IDTicket = TD.IDTicket)
  LEFT JOIN " . DBPREFIX . "Department D ON(TD.IDDepartment = D.IDDepartment)
  LEFT JOIN " . DBPREFIX . "DepartmentSupporter DS ON(DS.IDDepartment = D.IDDepartment)
  LEFT JOIN " . DBPREFIX . "Supporter S ON(S.IDSupporter = DS.IDSupporter)
  LEFT JOIN " . DBPREFIX . "User U ON (S.IDUser = U.IDUser)
WHERE
  T.IDTicket = $IDTicket
AND
  TD.IDTicket = $IDTicket
AND
  TD.BoReader = 0
GROUP BY
  T.IDTicket";

    $this->execSQL($StSQL);
    $ArHeader = $this->getResult('string');

    ###    FIX ME FIX ME FIX ME FIX ME    ###

    if (empty($ArHeader)) {
      $StSQL = '
SELECT
  T.*, U.StName
FROM
'.DBPREFIX.'Ticket T
  LEFT JOIN '.DBPREFIX.'TicketSupporter TS ON (TS.IDTicket = T.IDTicket)
  LEFT JOIN '.DBPREFIX.'Supporter S ON (TS.IDSupporter = S.IDSupporter)
  LEFT JOIN '.DBPREFIX."User U ON (S.IDUser = U.IDUser)
WHERE
  T.IDTicket = $IDTicket
GROUP BY
  T.IDTicket";
      $this->execSQL($StSQL);
      $ArHeader = $this->getResult('string');
    }

    return $ArHeader[0];
  }

  /**
   * List all messages of the ticket given
   *
   * @param int $IDTicket
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public function listTicketMessages($IDTicket) {
    $StSQL = "
SELECT
  M.*, U.StName as SentBy
FROM
  " . DBPREFIX . "Message M
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDTicket = M.IDTicket)
  LEFT JOIN " . DBPREFIX . "User U ON (M.IDUser = U.IDUser)
WHERE
  T.IDTicket = $IDTicket";

    $this->execSQL($StSQL);
    $ArMessages = $this->getresult('string');
    return $ArMessages;
  }

  /**
   * set the supporter who is the owner of the ticket
   *
   * @param int $IDTicket
   * @param int $IDSupporter
   * @param int $IDUser
   * @return int  Affected Rows
   */
  public function setTicketOwner($IDTicket, $IDSupporter, $IDUser) {
    $ArData = F1DeskUtils::getUserData($IDSupporter);
    $this->addMessage($IDUser,$IDTicket, $ArData['StName'] . MSG_OWNED, 1, 2);

    $StTableName = DBPREFIX . 'Ticket';
    $ArFields = array('IDSupporter' => $IDSupporter);
    $NumRows = $this->updateTable($StTableName, $ArFields, "IDTicket = $IDTicket", 1);
    return $NumRows;
  }

  /**
   * Check's if the user have permission to download the file given
   *
   * @param int $IDAttachment
   * @param int $ID
   * @return array [Permission and Link]
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public function canDownload($IDAttachment, $ID) {
    $StSQL = '
SELECT
  A.StLink, A.StFile, A.ByFile,
IF(EXISTS(
    SELECT
      T.IDTicket
    FROM
      ' . DBPREFIX . 'Attachment A
    LEFT JOIN ' . DBPREFIX . 'Message M ON (A.IDMessage = M.IDMessage)
    LEFT JOIN ' . DBPREFIX . 'Ticket T ON (M.IDTicket = T.IDTicket)
    LEFT JOIN ' . DBPREFIX . "User U ON (T.IDUser = U.IDUser)
    WHERE
      A.IDAttachment = $IDAttachment
    AND
      U.IDUser = $ID
  ),'true','false')
AS
  BoPermission
FROM
  ". DBPREFIX ."Attachment A
WHERE
  A.IDAttachment = $IDAttachment";
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');

    return array_shift($ArResult);
  }

  /**
   * Return a preview anser for the user
   *
   * @param integer $IDUser
   * @param text $TxMessage
   */
  public function getPreviewAnswer($IDUser, $TxMessage, $StMessageType) {
    $ArData = F1DeskUtils::getUserHeaderSign($IDUser);
    if ($StMessageType == 'INTERNAL')
      return $TxMessage;
    else
      return $ArData['TxHeader'] . "\n\n" . $TxMessage . "\n\n" . $ArData['TxSign'];
  }

  /**
   * get all tickets that attached this ticket
   *
   * @param integer $IDTicket
   * @return array
   */
  public function getTicketsAttached($IDTicket){
    $StSQL = '
SELECT
  AT.IDTicket
FROM
  '.DBPREFIX.'AttachedTicket AT
  LEFT JOIN '.DBPREFIX.'Ticket T ON (T.IDTicket = AT.IDTicket)
WHERE
  AT.IDAttachedTicket = ' . $IDTicket ;
    $this->execSQL($StSQL);

    $ArResult = $this->getResult('string');

    return  $ArResult;
  }

  /**
   * Get the attachments of the message given
   *
   * @param int $IDMessage
   * @return array
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  private function _getAttachments($IDMessage) {
    $StSQL = '
SELECT
  A.*
FROM
  ' . DBPREFIX . 'Attachment A
  LEFT JOIN ' . DBPREFIX . "Message M ON (A.IDMessage = M.IDMessage)
WHERE
  M.IDMessage = $IDMessage";
    $this->execSQL($StSQL);
    $ArReturn = $this->getResult('string');

    return $ArReturn;
  }

  /**
	 * Get all attachments from all messagens of a call
	 *
	 * @param int $IDTicket
	 * @return array
	 *
	 * @author Matheus Ashton <matheus@digirati.com.br>
	 */
	public function getAttachments($IDTicket) {
	  $ArAttachments = array();

	  $ArMessages = $this->listTicketMessages($IDTicket);

	  foreach ($ArMessages as $ArMessage) {
	    $ArAttachment = $this->_getAttachments($ArMessage['IDMessage']);
	    if (! empty($ArAttachment))
	    $ArAttachments[$ArMessage['IDMessage']] = $ArAttachment;
	  }
	  return $ArAttachments;
	}

  /**
   * get all attacheds tickets from a ID given
   *
   * @param integer $IDTicket
   * @return array
   */
  public function getAttachedTickets($IDTicket){
    $StSQL = '
SELECT
  AT.IDAttachedTicket
FROM
  '.DBPREFIX.'AttachedTicket AT
  LEFT JOIN '.DBPREFIX.'Ticket T ON (T.IDTicket = AT.IDTicket)
WHERE
  AT.IDTicket = ' . $IDTicket ;
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult;
  }

  /**
   * get all departments of a ticket
   *
   * @param integer $IDTicket
   * @return array
   */
  public function getTicketDepartments($IDTicket){
    $StSQL = '
SELECT
  D.*
FROM
  '.DBPREFIX.'Ticket T
  LEFT JOIN '.DBPREFIX.'TicketDepartment TD ON (T.IDTicket = TD.IDTicket)
  LEFT JOIN '.DBPREFIX.'Department D ON (D.IDDepartment = TD.IDDepartment)
WHERE
    T.IDTicket = ' . $IDTicket . '
  AND
    TD.BoReader = 0 ';
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult;
  }

  /**
   * get all departments who just see a ticket
   *
   * @param integer $IDTicket
   * @return array
   */
  public function getTicketDepartmentsReader($IDTicket){
    $StSQL = '
SELECT
  D.*
FROM
  '.DBPREFIX.'Ticket T
  LEFT JOIN '.DBPREFIX.'TicketDepartment TD ON (T.IDTicket = TD.IDTicket)
  LEFT JOIN '.DBPREFIX.'Department D ON (D.IDDepartment = TD.IDDepartment)
WHERE
    T.IDTicket = ' . $IDTicket . '
  AND
    TD.BoReader = 1 ';
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult;
  }

  /**
   * get who users a ticket was sent to
   *
   * @param integer $IDTicket
   * @return array
   */
  public function getTicketDestination($IDTicket){
    $StSQL = '
SELECT
  U.*
FROM
  '.DBPREFIX.'User U
  LEFT JOIN '.DBPREFIX.'Supporter S ON (U.IDUser = S.IDUser)
  LEFT JOIN '.DBPREFIX.'TicketSupporter TS ON (S.IDSupporter = TS.IDSupporter)
  LEFT JOIN '.DBPREFIX.'Ticket T ON (T.IDTicket = TS.IDTicket)
WHERE
    T.IDTicket = ' . $IDTicket . '
  AND
    TS.BoReader = 0 ';
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult;
  }

  /**
   * get who users can see a ticket
   *
   * @param integer $IDTicket
   * @return array
   */
  public function getTicketReaders($IDTicket){
    $StSQL = '
SELECT
  U.*
FROM
  '.DBPREFIX.'User U
  LEFT JOIN '.DBPREFIX.'Supporter S ON (U.IDUser = S.IDUser)
  LEFT JOIN '.DBPREFIX.'TicketSupporter TS ON (S.IDSupporter = TS.IDSupporter)
  LEFT JOIN '.DBPREFIX.'Ticket T ON (T.IDTicket = TS.IDTicket)
WHERE
    T.IDTicket = ' . $IDTicket . '
  AND
    TS.BoReader = 1 ';
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult;
  }

  /**
   * verify if a ticket exists
   *
   * @param int $IDTicket
   * @return bool
   */
  public function ticketExists($IDTicket) {
    $StSQL = '
SELECT
  IDTicket
FROM
  '.DBPREFIX.'Ticket T
WHERE
  T.IDTicket = ' . $IDTicket;

    $this->execSQL($StSQL);
    $ItNumRows = $this->getNumRows();
    return ($ItNumRows > 0);
  }

  /**
   * verify if a user can see the ticket
   *
   * @param int $IDTicket
   * @param int $IDUser
   *
   * @return bool
   */
  public function isVisible($IDTicket, $IDUser) {
    $StSQL = '
SELECT
  U.*
FROM
  '.DBPREFIX.'User U
  LEFT JOIN '.DBPREFIX.'Ticket T ON (T.IDUser= U.IDUser)
WHERE
  T.IDTicket = ' . $IDTicket . '
AND
  U.IDUser = ' . $IDUser;

    $this->execSQL($StSQL);
    $ItNumRows = $this->getNumRows();
    return ($ItNumRows > 0);
  }

  /**
   * change the department in which the ticket is allocated
   *
   * @param unknown_type $IDTicket
   * @param unknown_type $IDDepartment
   */
  public function changeDepartment($IDTicket, $IDDepartment) {
    $StTableName = DBPREFIX.'TicketDepartment';
    $ArData = array('IDDepartment' => $IDDepartment);
    $ItAffected = $this->updateTable($StTableName,$ArData,"IDTicket = $IDTicket",1);
    $StSQL = '
SELECT
  StDepartment
FROM
'.DBPREFIX."Department
WHERE
IDDepartment = $IDDepartment";
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('num');
    $StDepartment = $ArResult[0][0];
    $StSysMessage = CHANGE_DEPARTMENT . $StDepartment;
    $this->addMessage(getSessionProp('IDUser'),$IDTicket,$StSysMessage,1,2);
    return ($ItAffected < 0) ? false : true;
  }

  /**
   * Send an email to all users related to the ticket given for each new message.
   *
   * @param int $IDTicket
   * @return Boolean
   */
  public function _sendNotifyMessage($IDTicket) {
    $ArUsersDepartment = array();
    $ArUsersDepartmentReader = array();

    #
    # Preparing mail header
    #
    $ArEmails = $ArUsersDepartment = $ArUsersDepartmentReader = array();
    $MailHandler = new MailHandler();
    $MailHandler->setHTMLBody(true);
    $StHeaders = "MIME-Version: 1.0\r\n";
    $StHeaders .= "Content-type: text/html; charset=utf-8\r\n";


    #
    # Get the users related with the ticket
    #
    $ArRecipients = $this->getTicketDestination($IDTicket);
    $ArReaders = $this->getTicketReaders($IDTicket);
    $StSQL = '
SELECT
  StEmail, BoNotify
FROM
  '.DBPREFIX.'User U
LEFT JOIN '.DBPREFIX."Ticket T ON (T.IDUser = U.IDUser)
WHERE
  T.IDTicket = $IDTicket";
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');

    #
    # Get the department related with the ticket and his supporters
    #
    $ArDepartment = array_shift($this->getTicketDepartments($IDTicket));
    $ArDepartmentReaders = array_shift($this->getTicketDepartmentsReader($IDTicket));
    if (isset($ArDepartment['IDDepartment']) && isset($ArDepartmentReaders['IDDepartment'])) {
      $ArUsersDepartment = F1DeskUtils::getDepartmentSupporters($ArDepartment['IDDepartment']);
      $ArUsersDepartmentReader = F1DeskUtils::getDepartmentSupporters($ArDepartmentReaders['IDDepartment']);
    }


    #
    # Merging all users in one array
    #
    $ArUsers = array_merge($ArRecipients,$ArReaders);
    $ArUsersDepart = array_merge($ArUsersDepartment,$ArUsersDepartmentReader);
    $ArFinal = array_merge($ArUsers,$ArUsersDepart);
    $ArFinal = array_merge($ArFinal,$ArResult);


    #
    # Insert Message and Subject and strip the emails that already are in array
    #
    foreach ($ArFinal as $User) {
      if($User['BoNotify']) {
        if (array_search($User['StEmail'],$ArEmails) === false)
          $ArEmails[] = $User['StEmail'];
      }
    }
    $StSubject = str_replace('###TKTNUM###',$IDTicket,NOTIFY_SUBJ);
    $StMessage = str_replace('###TKTNUM###',$IDTicket,NOTIFY_MESSAGE);
    $BoResult = $MailHandler->sendMail($ArEmails,$StSubject,$StMessage,$StHeaders);

    return $BoResult;
  }

  /**
   * gets the category of a ticket
   *
   * @param integer $IDTicket
   * @return string
   */
  public function getTicketCategory($IDTicket){
    $StSQL = '
SELECT
  C.StCategory
FROM
  '.DBPREFIX.'Ticket T
  LEFT JOIN
    '.DBPREFIX.'Category C ON (C.IDCategory = T.IDCategory)
WHERE
    T.IDTicket = ' . $IDTicket ;
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult[0]['StCategory'];
  }

  /**
   * gets the priority of a ticket
   *
   * @param integer $IDTicket
   * @return string
   */
  public function getTicketPriority($IDTicket){
    $StSQL = '
SELECT
  P.StPriority
FROM
  '.DBPREFIX.'Ticket T
  LEFT JOIN
    '.DBPREFIX.'Priority P ON (P.IDPriority = T.IDPriority)
WHERE
    T.IDTicket = ' . $IDTicket ;
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult[0]['StPriority'];
  }

  /**
   * gets the type of a ticket
   *
   * @param integer $IDTicket
   * @return string
   */
  public function getTicketType($IDTicket){
    $StSQL = '
SELECT
  Ty.StType
FROM
  '.DBPREFIX.'Ticket T
  LEFT JOIN
    '.DBPREFIX.'Type Ty ON (Ty.IDType = T.IDType)
WHERE
    T.IDTicket = ' . $IDTicket ;
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    return  $ArResult[0]['StType'];
  }
  
  /*colocar esse aqui na classe de busca!!!!!!!!!!!!!!!!!!!*/
  public function reportTicketsByDepartment(){
    $StSQl = ' 
SELECT 
  D.StDepartment as StDepartment, count(TD.IDTicket) as ItTotal, group_concat(T.StSituation) as ArSituation
FROM 
  '.DBPREFIX.'Department D
    LEFT JOIN 
      '.DBPREFIX.'TicketDepartment TD on(D.IDDepartment = TD.IDDepartment)
    LEFT JOIN 
      '.DBPREFIX.'Ticket T on(T.IDTicket = TD.IDTicket)
WHERE
  T.StSituation != "CLOSED"
GROUP BY 
  D.IDDepartment';
    $this->execSQL($StSQl);
    $ArResult = $this->getResult('string');
    foreach ($ArResult as &$ArSingleResult){
      $ArSituation = explode(',',$ArSingleResult['ArSituation']);
      $ArSingleResult['ArSituation'] = array_count_values($ArSituation);
    }
    
    return $ArResult;
  }
  
  public function reportAnswersByDepartment(){
    $StSQL = '
SELECT 
  D.StDepartment as StDepartment, count(M.IDTicket) as ItTotal
FROM 
  '.DBPREFIX.'Department D
    LEFT JOIN 
      '.DBPREFIX.'TicketDepartment TD on(D.IDDepartment = TD.IDDepartment)
    LEFT JOIN 
      '.DBPREFIX.'Ticket T on(T.IDTicket = TD.IDTicket)
    LEFT JOIN
      '.DBPREFIX.'Message M on(T.IDTicket = M.IDTicket)
GROUP BY 
  D.IDDepartment
    ';
    $this->execSQL($StSQL);
    return $this->getResult('string');
  }
  
  public function reportAnswerBySupporter(){
    $StSQL = '
SELECT
  U.StName, U.StEmail, count(M.IDTicket) as ItTotal
FROM 
  '.DBPREFIX.'User U
    LEFT JOIN 
      '.DBPREFIX.'Message M on(U.IDUser = M.IDUser)
GROUP BY 
  U.IDUser
    ';
    $this->execSQL($StSQL);
    return $this->getResult('string');
  }
  
  public function reportSupportersByDepartments(){
    $StSQL = '
SELECT
  D.StDepartment, D.IDDepartment, group_concat(U.StName," <",U.StEmail,">" ) as Supporter
FROM
  '.DBPREFIX.'Department D
  LEFT JOIN  
    '.DBPREFIX.'DepartmentSupporter DS on(DS.IDDepartment = D.IDDepartment)
  LEFT JOIN
    '.DBPREFIX.'Supporter S on(S.IDSupporter = DS.IDSupporter)
  LEFT JOIN
    '.DBPREFIX.'User U on(U.IDUser = S.IDUser)
GROUP BY
  D.IDDepartment
    ';
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');
    foreach ($ArResult as &$ArDepartment){
      $ArSupporter = explode(',', $ArDepartment['Supporter']);
      $ArDepartment['Supporter'] = (empty($ArSupporter[0]))?array():$ArSupporter;
    }
    
    return $ArResult;
  }
  
}
?>