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

      #
      # get file information
      #
      $StFile = $Files[$StField]['name'];
      $StTmp = $Files[$StField]['tmp_name'];
      $ItSize = $Files[$StField]['size'];

      #
      # checking if file is valid
      #
      if ( ! is_uploaded_file($StTmp)) {
        throw new ErrorHandler(EXC_CALL_NOTUPLOADFILE);
      }

      if ($ItSize > UPLOAD_MAX_SIZE) {
        throw new ErrorHandler(EXC_CALL_MAXFILESIZE);
      }

      if ( ! $StFile = $this->_validateFile($StFile) ) {
        throw new ErrorHandler(EXC_CALL_INVALIDTYPE);
      }

      #
      # checking if upload its to db or ftp
      #
      if (UPLOAD_OPT == 'DB') {

        #
        # inserting file content on DB
        #
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

        #
        # uploading file to ftp
        #
        $StUploadedFile = UPLOADDIR . '/' . $this->_generateFileName();

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

    $ArCharOld = explode(' ', 'À Á Â Ã Ä Å Æ Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö Ø Ù Ú Û Ü Ý Þ ß à á â ã ä å æ ç è é ê ë ì í î ï ð ñ ò ó ô õ ö ø ù ú û ý ý þ ÿ Ŕ ŕ' );
    $ArCharNew = explode(' ', 'a a a a a a a c e e e e i i i i d n o o o o o o u u u u y b b a a a a a a a c e e e e i i i i d n o o o o o o u u u y y b y R r');

    $ArReplace = array_combine( $ArCharOld, $ArCharNew );
    $StFile = strtr( $StFile, $ArReplace );

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
   * Check if a Ticket was read by a supporter
   *
   * @param integer $ItIDSupporter
   * @param integer $ItIDTicket
   *
   * @return array
   * @author Mario Vítor <mario@digirati.com.br>
   */
  public function isRead($ItIDSupporter, $ItIDTicket){

  	$StSQL = "
SELECT
	count(R.IDTicket) as Total
FROM
	Ticket T
	LEFT JOIN isRead R
  	on (T.IDTicket = R.IDTicket)
 	LEFT JOIN Supporter S
  	on (S.IDSupporter = R.IDSupporter)
WHERE
		( R.IDTicket = $ItIDTicket )
	AND
		( R.IDSupporter = $ItIDSupporter )
";
  	$this->execSQL($StSQL);
  	$ArResult = $this->getResult("string");

  	return $ArResult;

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
  public function setAsRead($ItIDSupporter, $ItIDTicket){

    if ( empty($ItIDSupporter) || empty($ItIDTicket) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDREADID);
    }

    #
    # array -> fields of table Read
    #
    $ArFields = array(  'IDSupporter', 'IDTicket'  );

    #
    # array -> fields of inserting
		# ( $this->insertIntoTable funciton needs a array of arrays to insert )
    #
    $ArInsert = array(  array(  $ItIDSupporter, $ItIDTicket  )  );

    $StTableName = DBPREFIX . 'isRead';

    $ItAffected = $this->insertIntoTable($StTableName, $ArFields, $ArInsert);

    return array(  (!$ItAffected)?'error':'sucess' => $ArInsert  );

  }

  /**
   *
   */
  public function notReadCount($IDDepartment, $IDUser, $StUserType){

  	switch ($StUserType) {
  		case "supporter":
  			$StSQL = "
SELECT
	COUNT( TD.IDTicket ) AS totalOpened
FROM
	Department D
LEFT JOIN	TicketDepartment TD
	on (D.IDDepartment = TD.IDDepartment)
LEFT JOIN Ticket T
	on (T.IDTicket = TD.IDTicket)
WHERE
	D.IDDepartment = $IDDepartment
		";
		  	$this->execSQL($StSQL);
		  	$ArResult = $this->getResult("string");
		  	$Opened = $ArResult[0]["totalOpened"];

		  	$StSQL = "
		SELECT
			COUNT( TD.IDTicket ) AS totalRead
		FROM
			Department D
		LEFT JOIN	TicketDepartment TD
			on (D.IDDepartment = TD.IDDepartment)
		LEFT JOIN Ticket T
			on (T.IDTicket = TD.IDTicket)
		LEFT JOIN isRead R
			on (R.IDTicket = T.IDTicket)
		LEFT JOIN Supporter S
			on (S.IDSupporter = R.IDSupporter)
		WHERE
				( S.IDSupporter = $IDUser )
			AND
				( D.IDDepartment = $IDDepartment )
		  	";
		  	$this->execSQL($StSQL);
		  	$ArResult = $this->getResult("string");
		  	$Read = $ArResult[0]["totalRead"];

		  	return array(
		  		"returnType" => "supporter",
		  		"returnContent" => array($Opened , $Read)
	  		);
			break;

  		case "client":
  			$StSQL = "
SELECT
	COUNT(T.IDTicket) as TicketCount
FROM
	Ticket T
LEFT JOIN User U
	on (U.IDUser = T.IDUser)
WHERE
	U.IDUser = $IDUser
 AND
	T.StSituation = 'WAITING_USER'
  			";

  			$this->execSQL($StSQL);
		  	$ArResult = $this->getResult("string");
		  	$ItOpened = $ArResult[0]['TicketCount'];

		  	return array(
		  		"returnType" => "client",
		  		"returnContent" => array( "opened"=> $ItOpened, "closed"=> 0 )
		  	);
  		break;

  		default:
  			throw new ErrorHandler(EXC_CALL_INVALIDREADID);
			break;
  	}

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
  public function ignoreTicket($ItIDUser, $ItIDCall){

    if ( empty($ItIDUser) || empty($ItIDCall) ) {
    	throw new ErrorHandler(EXC_CALL_INVALIDIGNOREID);
    }

    #
    # array -> fields of table Read
    #
    $ArFields = array(  'IDUser', 'IDCall'  );

    #
    # array -> fields of inserting
    # ( $this->insertIntoTable funciton needs a array of arrays to insert )
    #
    $ArInsert = array(  array(  $ItIDUser, $ItIDCall  )  );

    $StTableName = DBPREFIX . 'Ignored';

    $ItAffected = $this->insertIntoTable($StTableName, $ArFields, $ArInsert);

    return array(  (!$ItAffected)?'error':'sucess' => $ArInsert  );

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

    #
    # message types availables
    #
    $ArTypes = array( 'NORMAL' , 'INTERNAL' , 'SYSTEM');

    #
    # preparing to insert on the table
    #
    $StTableName = DBPREFIX . 'Message';
    $ArFields = array( 'TxMessage' , 'DtSended' , 'BoAvailable' , 'EnMessageType' , 'IDTicket' , 'IDUser' );
    $ArValues = array( $StMessage , date('Y-m-d H:i:s',time()) , $BoAvailable, $ArTypes[$ItMsgType], $IDTicket, $IDUser );

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
   * list the tickets related to an especific user
   *
   * @param int $IDUser
   * @return array
   */
  public function listUserTickets($IDUser){

    if ( !is_int($IDUser) ) {
      throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
    }

    $StSQL = '
SELECT
  T.*
FROM
  ' . DBPREFIX . 'Ticket T
WHERE
  T.IDUser = ' . $IDUser . '
AND
  T.StSituation != "CLOSED"';

    $this->execSQL($StSQL);
    $ArResults = $this->getResult("string");

    return $ArResults;


  }

  /**
   * list the calls related to an especific supporter
   *
   * @param int $IDSupporter
   * @param int $isInternal
   *
   * @return array
   */
  public function listSupporterTickets($IDSupporter, $isInternal = false){

    $StAdicionalWhere = '';
    $ArTickets = $ArResults1 = $ArResults2 = array();

    if ( !is_int($IDSupporter) ) {
      throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
    }

    if ($isInternal) {

      $StAdicionalWhere = '
AND
  BoInternal = 1';

      $StSQL = '
SELECT
  T.*
FROM
  ' . DBPREFIX . 'Ticket T
  LEFT JOIN ' . DBPREFIX . 'TicketSupporter TS ON(TS.IDTicket = T.IDTicket)
  LEFT JOIN ' . DBPREFIX . 'Supporter S ON(TS.IDSupporter = S.IDSupporter)
WHERE
  S.IDSupporter = ' . $IDSupporter . '
AND
  T.StSituation != "CLOSED"' .
$StAdicionalWhere;

      $this->execSQL($StSQL);
      $ArResults1 = $this->getResult("string");

    }

    $StSQL = '
SELECT
  T.*, D.StDepartment, D.IDDepartment
FROM
  ' . DBPREFIX . 'Ticket T
  LEFT JOIN ' . DBPREFIX . 'TicketDepartment TD ON(TD.IDTicket = T.IDTicket)
  LEFT JOIN ' . DBPREFIX . 'Department D ON(TD.IDDepartment = D.IDDepartment)
  LEFT JOIN ' . DBPREFIX . 'DepartmentSupporter DS ON(DS.IDDepartment = D.IDDepartment)
  LEFT JOIN ' . DBPREFIX . 'Supporter S ON(S.IDSupporter = DS.IDSupporter)
WHERE
  S.IDSupporter = ' . $IDSupporter . '
AND
  T.StSituation != "CLOSED"' .
$StAdicionalWhere;

    $this->execSQL($StSQL);
    $ArResults2 = $this->getResult("string");

    $ArAux = array_merge($ArResults1,$ArResults2);

    foreach ($ArAux as $ArRecord) {
      if ( array_key_exists('IDDepartment',$ArRecord) ) {
        $ArTickets[$ArRecord['IDDepartment']][] = $ArRecord;
      } else {
        $ArTickets['Others'][] = $ArRecord;
      }
    }

    ksort($ArTickets);

    return $ArTickets;

  }

  /**
   * list the calls related to an especific department
   *
   * @param int $IDSupporter
   * @param int $isInternal
   *
   * @return array
   */
  public function listTickets($IDDepartment, $IDSupporter){

  	if ( is_null( $IDDepartment ) ) {
  		throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
  	}

		$StSQL = "
SELECT
  T.*,
  D.StDepartment,
  U.Stname as StSupporter,
  IF(EXISTS (SELECT R.IDTicket FROM isRead R WHERE R.IDSupporter = $IDSupporter AND R.IDTicket = T.IDTicket), 'READ', 'NOT_READ') AS isRead
FROM
  " . DBPREFIX . "User U
  LEFT JOIN " . DBPREFIX . "Supporter S ON (U.IDUser = S.IDUser)
  LEFT JOIN " . DBPREFIX . "Ticket T ON (T.IDSupporter = S.IDSupporter)
  LEFT JOIN " . DBPREFIX . "TicketDepartment TD ON(TD.IDTicket = T.IDTicket)
  LEFT JOIN " . DBPREFIX . "Department D ON(TD.IDDepartment = D.IDDepartment)
WHERE
  D.IDDepartment = $IDDepartment
AND
  T.StSituation != 'CLOSED'
AND
  NOT EXISTS(SELECT I.IDTicket FROM Ignored I WHERE I.IDSupporter = $IDSupporter AND I.IDTicket = T.IDTicket)";

		$this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

		return $ArTickets;
  }

  /**
   * Enter description here...
   *
   * @param unknown_type $IDUser
   * @param unknown_type $StStatus
   */
  public function listClientTickets($IDUser, $BoOpened = true){

  	if ( is_null( $IDUser ) ) {
  		throw new ErrorHandler(EXC_CALL_INVALIDLISTOFCALLS);
  	}

  	$StConditionStatus = ( ($BoOpened)?'!=':'=' ) . ' "CLOSED" ';

  	$StSQL = "
SELECT T.*, U2.StName as StSupporterName
FROM Client C
  LEFT JOIN User U on (C.IDUser = U.IDUser)
  LEFT JOIN Ticket T on (T.IDUser = U.IDUser)
  LEFT JOIN Supporter S on (S.IDSupporter = T.IDSupporter)
  LEFT JOIN User U2 on (S.IDUser = U2.IDUser)
WHERE
  T.StSituation $StConditionStatus
AND
  U.IDUser = $IDUser
  	";

  	$this->execSQL($StSQL);
		$ArTickets = $this->getResult("string");

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
    if (isset($_SESSION['isSupporter']) && $_SESSION['isSupporter'] == true) {
      $this->_supporterAnswer($IDWriter,$IDTicket,$TxMessage, $StMsgType, $ArFiles);
    } else {
      $this->_clientAnswer($IDWriter,$IDTicket,$TxMessage, $StMsgType, $ArFiles);
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

    if ($StMsgType != 'SATISFACTION' || $StMsgType == '0') {
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
LEFT JOIN
  " . DBPREFIX . "TicketDepartment TD ON(T.IDTicket = TD.IDTicket)
LEFT JOIN
  " . DBPREFIX . "Department D ON(TD.IDDepartment = D.IDDepartment)
WHERE
  T.IDTicket = $IDTicket";
    $this->execSQL($StSQL);
    $ArHeader = $this->getResult('string');

    return $ArHeader;
  }

  /**
   * List all messages of the ticket given
   *
   * @param unknown_type $IDTicket
   */
  public function listTicketMessages($IDTicket) {
    $StSQL = "
SELECT
M.*, U.StName as SentBy
FROM
" . DBPREFIX . "Message M
LEFT JOIN
  " . DBPREFIX . "Ticket T ON (T.IDTicket = M.IDTicket)
LEFT JOIN
  " . DBPREFIX . "User U ON (M.IDUser = U.IDUser)
WHERE T.IDTicket = $IDTicket";
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
}
?>