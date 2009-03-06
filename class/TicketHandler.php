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
   * get the tickets read by a supporter, from a specific department
   *
   * @param integer $IDDepartment
   * @param integer $IDUser
   * @param array   $$TicketList
   *
   * @return array
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public function getReadTickets($IDDepartment, $IDUser, $TicketList = '') {

    if ($TicketList === '') {
    	$StSQL = "
SELECT
	T.IDTicket
FROM
	Ticket T
	LEFT JOIN isRead R ON (T.IDTicket = R.IDTicket)
	LEFT JOIN User U ON (U.IDUser = R.IDUser)
 	LEFT JOIN Supporter S ON (S.IDUSer = U.IDUSer)
 	LEFT JOIN DepartmentSupporter DS ON (DS.IDSupporter = S.IDSupporter)
 	LEFT JOIN Department D ON (D.IDDepartment = DS.IDDepartment)
WHERE
  D.IDDepartment = $IDDepartment
AND
  U.IDUser = $IDUser
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')";
    } else {
      $Tickets = implode(', ',$TicketList);
      $Tickets = empty($Tickets) ? "''" : $Tickets;
      $StSQL = "
SELECT
	T.IDTicket
FROM
	Ticket T
	LEFT JOIN isRead R ON (T.IDTicket = R.IDTicket)
	LEFT JOIN User U ON (U.IDUser = R.IDUser)
WHERE
  T.IDTicket IN ($Tickets)
AND
  U.IDUser = $IDUser
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')";
    }
  	$this->execSQL($StSQL);
  	$ArResult = $this->getResult("string");

  	$ArRead = F1DeskUtils::sortByID($ArResult, 'IDTicket');

  	return $ArRead;

  }

  /**
   * get the tickets read by a supporter, from a specific department
   *
   * @param integer $IDDepartment
   * @param integer $IDUser
   *
   * @return array
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
   public function getUserReadTickets($IDUser) {

  	$StSQL = "
SELECT
	T.IDTicket
FROM
	Ticket T
	LEFT JOIN isRead R ON (T.IDTicket = R.IDTicket)
	LEFT JOIN User U ON (U.IDUser = R.IDUser)
WHERE
  U.IDUser = $IDUser
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP','WAITING_USER')";

  	$this->execSQL($StSQL);
  	$ArResult = $this->getResult("string");

  	$ArRead = F1DeskUtils::sortByID($ArResult, 'IDTicket');

  	return $ArRead;

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
  isRead
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
   * get the not read quantity, for users
   *
   * @param int $IDUser
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public function UserNotReadCount($IDUser) {
    $StSQLOpened = "
SELECT
	COUNT( T.IDTicket ) AS totalOpened
FROM
	User U
  LEFT JOIN Ticket T ON (T.IDUser = U.IDUser)
WHERE
	U.IDUser = $IDUser
AND
  T.StSituation = 'WAITING_USER'";

  	  $StSQLRead = "
SELECT
	COUNT( T.IDTicket ) AS totalRead
FROM
	Ticket T
	LEFT JOIN isRead R ON (R.IDTicket = T.IDTicket)
	LEFT JOIN User U ON (U.IDUser = R.IDUser)
WHERE
	U.IDUser = $IDUser
AND
  T.StSituation = 'WAITING_USER'";

  	$this->execSQL($StSQLOpened);
  	$ArResult = $this->getResult("string");
  	$Opened = $ArResult[0]["totalOpened"];

  	$this->execSQL($StSQLRead);
  	$ArResult = $this->getResult("string");
  	$Read = $ArResult[0]["totalRead"];

  	$notRead = $Opened - $Read;

  	return array('opened' => array('notRead'  => $notRead), 'closed' => array('notRead'  => $notRead));
  }

  /**
   * get the not read quantity, for supporters
   *
   * @param int $IDUser
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public function notReadCount($IDUser){
    $ArNotRead = $ArRead = array();

		$StSQL = "
SELECT
  D.IDDepartment, COUNT( T.IDTicket ) AS notRead
FROM
  User U
  LEFT JOIN " . DBPREFIX . "Supporter S ON (S.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "DepartmentSupporter DS ON (DS.IDSupporter = S.IDSupporter)
  LEFT JOIN " . DBPREFIX . "Department D ON (D.IDDepartment = DS.IDDepartment)
  LEFT JOIN " . DBPREFIX . "TicketDepartment TD ON (D.IDDepartment = TD.IDDepartment)
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDTicket = TD.IDTicket)
WHERE
  U.IDUser = $IDUser
AND
  NOT EXISTS(SELECT I.IDTicket FROM " . DBPREFIX . "Ignored I WHERE I.IDTicket = T.IDTicket AND I.IDSupporter = S.IDSupporter)
AND
  NOT EXISTS(SELECT IR.IDTicket FROM " . DBPREFIX . "isRead IR WHERE IR.IDTicket = T.IDTicket AND IR.IDUser = U.IDUser)
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')
GROUP BY
  D.IDDepartment";

  	$this->execSQL($StSQL);
  	$ArNotRead1 = $this->getResult("string");

		$StSQL = "
SELECT
  D.IDDepartment, COUNT( T.IDTicket ) AS notRead
FROM
  User U
  LEFT JOIN " . DBPREFIX . "Supporter S ON (S.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "DepartmentSupporter DS ON (DS.IDSupporter = S.IDSupporter)
  LEFT JOIN " . DBPREFIX . "Department D ON (D.IDDepartment = DS.IDDepartment)
  LEFT JOIN " . DBPREFIX . "TicketDepartment TD ON (D.IDDepartment = TD.IDDepartment)
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDTicket = TD.IDTicket)
WHERE
  U.IDUser = $IDUser
AND
  EXISTS(SELECT B.IDTicket FROM " . DBPREFIX . "Bookmark B WHERE B.IDTicket = T.IDTicket AND B.IDSupporter = S.IDSupporter)
AND
  NOT EXISTS(SELECT IR.IDTicket FROM " . DBPREFIX . "isRead IR WHERE IR.IDTicket = T.IDTicket AND IR.IDUser = U.IDUser)
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')
GROUP BY
  D.IDDepartment";

		$this->execSQL($StSQL);
  	$ArNotRead2 = $this->getResult("string");

  	foreach ($ArNotRead2 as &$ArRow) {
  	  $ArRow['IDDepartment'] = 'bookmark';
  	}

  	$ArNotRead = array_merge($ArNotRead1,$ArNotRead2);
		$ArNotRead = F1DeskUtils::sortByID($ArNotRead, 'IDDepartment');

  	return $ArNotRead;

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

    $ArHeaderSign = F1DeskUtils::getUserHeaderSign($IDUser);
    if (!empty($ArHeaderSign['TxHeader'])) {
      $ArHeaderSign['TxHeader'] .= '<br>';
    }
    if (!empty($ArHeaderSign['TxSign'])) {
      $ArHeaderSign['TxSign'] = '<br>' . $ArHeaderSign['TxSign'];
    }
    $StMessage = $ArHeaderSign['TxHeader'] . $StMessage . $ArHeaderSign['TxSign'];
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
  public function createSupporterTicket ($IDSupporter, $IDCategory, $IDPriority, $StTitle, $StMessage, $IDDepartment = '', $ArUsers = array(), $BoInternal = false, $ArFiles = array()) {

    if (!empty($ArUser) && $IDDpt == '') {
      throw new ErrorHandler(EXC_GLOBAL_EXPPARAM);
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

    if ($IDDepartment != '') {
      $this->insertIntoTable( DBPREFIX . 'TicketDepartment',
                              array('IDTicket','IDDepartment'),
                              array($IDTicket, $IDDepartment));
    }

    $StMsgType = ($BoInternal == true) ? 1 : 0;
    $IDMessage = $this->addMessage($IDSupporter, $IDTicket, $StMessage,$StMsgType);

    if (! empty($ArFiles)) {
      $this->attachFile($ArFiles,$IDMessage);
    }

    return true;
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
  public function createUserTicket ($IDUser, $IDCategory, $IDPriority, $StTitle, $StMessage, $IDDepartment,$ArFiles = array()) {

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

    return ($itReturn === false) ? false : true;
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
  public function attachTicket($ItIDTicket, $ItIDAttachedTicket) {

    if ( empty($ItIDAttachedTicket) || empty($ItIDTicket) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDATTACHID);
    }

    $StTableName = DBPREFIX . 'AttachedTicket';

    $ArFields = array(  'IDTicket', 'IDAttachedTicket'  );

    $ArInsert = array(  $ItIDTicket, $ItIDAttachedTicket  );

    $ItAffected = $this->insertIntoTable($StTableName, $ArFields, $ArInsert);

    return array(  (!$ItAffected)?'error':'sucess' => $ArInsert  );

  }

  /**
   * list the calls related to an especific department
   *
   * @param int $IDDepartment
   * @param int $IDSupporter
   * @param int $IDUser
   *
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public function listTickets($IDDepartment){

  	if ( is_null( $IDDepartment ) ) {
  		throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
  	}

		$StSQL = "
SELECT
  T.*,
  D.StDepartment,
  U.Stname as StSupporter
FROM
  " . DBPREFIX . "User U
  LEFT JOIN " . DBPREFIX . "Supporter S ON (S.IDUser = U.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T ON(T.IDSupporter = S.IDSupporter)
  LEFT JOIN " . DBPREFIX . "TicketDepartment TD ON(TD.IDTicket = T.IDTicket)
  LEFT JOIN " . DBPREFIX . "Department D ON(D.IDDepartment = TD.IDDepartment)
WHERE
  D.IDDepartment = $IDDepartment
AND
  T.StSituation IN ('NOT_READ','WAITING_SUP')";

		$this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

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
  public function listClientTickets($IDUser, $BoOpened = true){

  	if ( is_null( $IDUser ) ) {
  		throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
  	}

  	if ($BoOpened) {
      $StConditionStatus = "'NOT_READ','WAITING_SUP','WAITING_USER'";
  	} else {
  	  $StConditionStatus = "'CLOSED'";
  	}

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
  T.StSituation IN ($StConditionStatus)
AND
  U.IDUser = $IDUser
  	";

  	$this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

		$ArTickets = F1DeskUtils::sortByID($ArTickets, 'IDTicket');

		return $ArTickets;

  }

  /**
   * list the ignored tickets
   *
   * @param int $IDSupporter
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public function listIgnoredTickets($IDSupporter) {
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
  public function listBookmarkTickets($IDSupporter) {
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
    if (getSessionProp('isSupporter') && getSessionProp('isSupporter') == 'true') {
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

    $TxMessage = $this->replaceAlias($TxMessage,$IDWriter);

    #Add the reply
    $this->addMessage($IDUser, $IDTicket, $TxMessage, $BoReleased, $StMsgType);
    $IDMessage = $this->getID();
    if (!empty($ArFiles)) {
      $this->attachFile($ArFiles,$IDMessage);
    }

    if ($StMsgType != '3' || $StMsgType == '0') {
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
  T.IDTicket, T.StTitle, T.DtOpened, T.StSituation, T.IDSupporter, D.IDDepartment
FROM
  " . DBPREFIX . "Ticket T
  LEFT JOIN " . DBPREFIX . "TicketDepartment TD ON(T.IDTicket = TD.IDTicket)
  LEFT JOIN " . DBPREFIX . "Department D ON(TD.IDDepartment = D.IDDepartment)
WHERE
  T.IDTicket = $IDTicket";
    $this->execSQL($StSQL);
    $ArHeader = $this->getResult('string');

    return $ArHeader;
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
	 * Catch the alias in the message given replacing them with the corresponded answers
	 *
	 * @param str $TxMessage
	 * @return str $TxReturn
	 *
	 * @author Matheus Ashton <matheus@digirati.com.br>
	 */
	public function replaceAlias($TxMessage,$IDSupporter) {
	  $ArAlias = array();
    $ArResponses = F1DeskUtils::listCannedResponses($IDSupporter);
    foreach ($ArResponses as $Response) {
      $ArReplace[$Response['StAlias']] = F1DeskUtils::getResponseByAlias($Response['StAlias']);
    }
    $StMessage = strtr($TxMessage,$ArReplace);
    return $StMessage;
  }

  /**
   * Get the attachments of the message given
   *
   * @param int $IDMessage
   * @return array
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public function getAttachments($IDMessage) {
    $StSQL = '
SELECT
  A.*
FROM
  ' . DBPREFIX . 'Attachment A
LEFT JOIN
  ' . DBPREFIX . "Message M ON (A.IDMessage = M.IDMessage)
WHERE
  M.IDMessage = $IDMessage";
    $this->execSQL($StSQL);
    $ArReturn = $this->getResult('string');

    return $ArReturn;
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
  Attachment A
WHERE
  A.IDAttachment = $IDAttachment";
    $this->execSQL($StSQL);
    $ArResult = $this->getResult('string');

    return array_shift($ArResult);
  }
}
?>