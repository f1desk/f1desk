<?php

class UserHandler extends DBHandler {

  private $StHash = "";
  private $StLogin = "";

  /**
   * Class constructor
   *
   * @return VOID
   *
   * @author Matheus Ashton <matheus[at]digirati.com.br>
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * generates the hash, using the right method
   *
   * @param string $StHash
   * @param string $StData
   * @return string
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  private static function myHash($StHash, $StData) {
    $StHash = strtolower($StHash);
    switch ($StHash) {
      case 'md5' :
        $StEncryptedData = md5($StData);
        break;
      case 'sha1' :
        $StEncryptedData = sha1($StData);
        break;
    }
    
    return $StEncryptedData;
  }

  /**
   * Generate a Hash code and a crypt Password for a Password given
   *
   * @param string $StPwd
   *
   * @return array ( 'codHash' => code of Hash generated , 'cryptPwd' => Password crypted )
   *
   * @author Matheus Ashton <matheus[at]digirati.com.br>
   */
  public static function generateHash( $StPwd ) {
    $ItCodhash = mt_rand(0,1);
    $StHash = ($ItCodhash == 0) ? 'MD5' : 'SHA1';    
    $StPwd = self::myHash($StHash,$StPwd);
    $ArData = array('codHash' => $StHash, 'cryptPwd' => $StPwd);
    return $ArData;
  }  

  /**
   * Avoid SQL Injection on login
   *
   * @param  str $StArgs    Possible Malicious String
   *
   * @return str $StReturn  Safe string
   */
  public static function SQLInjectionHandle(&$StArgs) {
    $StArgs = addslashes($StArgs);

    $ArBloq = array("select"=>'',"drop"=>'',"delete"=>'',"insert"=>'',"update"=>'',"where"=>'',"having"=>'',"union"=>'',"'"=>'\'',"="=>'',"<"=>'',">"=>'');
    $StReturn = strstr(strtolower($StArgs),$ArBloq);

    return $StReturn;
  }

  /**
   * Creates the user's session and log him in
   *
   * @param unknown_type $StPwd
   * @return unknown
   */
  public function getLogged($StLogin, $StPwd) {
    UserHandler::SQLInjectionHandle($StLogin);
    UserHandler::SQLInjectionHandle($StPwd);
    $this->StLogin = $StLogin;
    $StSQL = "
SELECT
  IDUser, StPassword, StName, StEmail, StHash
FROM
  " . DBPREFIX . "User
WHERE
  StEmail = '{$this->StLogin}'";

    $this->execSQL($StSQL);
    $this->commit();
    if ($this->getNumRows() != 1) {
      throw new ErrorHandler(EXC_USER_NOTREG);
    }

    $ArResult = $this->getResult('string');

    if($ArResult[0]['StPassword'] == $this->myHash($ArResult[0]['StHash'],$StPwd)) {
      $StSQL = "
SELECT
  C.IDClient, S.IDSupporter
FROM
  " . DBPREFIX . "User U
LEFT JOIN
  " . DBPREFIX . "Supporter S ON (U.IDUser = S.IDUser)
LEFT JOIN
  " . DBPREFIX . "Client C ON (U.IDUser = C.IDUser)
WHERE
  U.IDUser = {$ArResult[0]['IDUser']}";
      $this->execSQL($StSQL);
      $ArResult = array_merge($ArResult, $this->getResult('string'));

      setSessionProp('StName',$ArResult[0]['StName']);
      setSessionProp('IDUser',$ArResult[0]['IDUser']);
      setSessionProp('StEmail',$ArResult[0]['StEmail']);
      setSessionProp('StHash',md5($ArResult[0]['IDUser'].$ArResult[0]['StName']));
      if (!isset($ArResult[1]['IDClient']) && isset($ArResult[1]['IDSupporter'])) {
        setSessionProp('isSupporter','true');
        setSessionProp('IDSupporter', $ArResult[1]['IDSupporter']);
      } else {
        setSessionProp('isSupporter', 'false');
        setSessionProp('IDClient', $ArResult[1]['IDClient']);
      }
      return true;
    } else {
      throw new ErrorHandler(EXC_USER_WRONGPASS);
    }
  }

  /**
   * Destroy the session to logout
   *
   */
  public static function logginOut() {
    session_destroy();
    unset($_SESSION);
  }

  /**
   * Sends a confirmation link to email address given
   *
   * @return unknown
   */
  public function sendConfirmationLink($StLogin) {
    $this->execSQL("
SELECT
  count(*) as ItCount
FROM
  " . DBPREFIX . "User
WHERE
  StEmail = '$StLogin'");

    $ArResult = $this->getResult('num');
    if($ArResult[0][0] != 1) {
      throw new ErrorHandler(EXC_USER_WRONGUSER);
    }
    $StLink = $this->generateConfirmationLink($StLogin);
    $ArMsg = $this->generateMessage($StLink);

    $MailHandler = new MailHandler();
    $MailHandler->setHTMLBody(true);
    $BoResult = $MailHandler->sendMail($StLogin,LNG_PASSREM_SUBJ,$ArMsg[0],$ArMsg[1]);
    return $BoResult;
  }

  /**
   * Generate the confirmation link
   *
   * @param unknown_type $StEmail
   * @return unknown
   */
  private function generateConfirmationLink($StEmail) {
    $StSQL = "
SELECT
  IDUser
FROM
  " . DBPREFIX . "User
WHERE
  StEmail = '$StEmail'";

    $this->execSQL($StSQL);
    $CodCLi = $this->getResult("num");
    $StEmail = urlencode($StEmail);
    $StDomain = "http://{$_SERVER['HTTP_HOST']}";
    $StDomain .= preg_replace("@{$_SERVER['DOCUMENT_ROOT']}@",'',APPDIR);
    $StChave = md5($StEmail . $CodCLi[0][0] . date('mdY',time()) . 'cH@v3¬r0c@$&nHa');

    $StLink = $StDomain . "passwordRemember.php?StEmail=$StEmail&StChave=$StChave";
    return $StLink;
  }

 /**
  * Generates the password's remember message
  *
  * @param   string   $StLink
  *
  * @return  array   $ArEmail  array com mensagem e cabe�alho do email
  *
  * @author Matheus Ashton <matheus@digirati.com.br>
  */
  private function generateMessage($StLink) {
    $StHeaders = "MIME-Version: 1.0\r\n";
    $StHeaders .= "Content-type: text/html; charset=utf-8\r\n";

    $StMessage = str_replace('#CONFIRMATIONLINK#',$StLink,LNG_MAIL_PASSREMMSG);
    $ArEmail = array($StMessage,$StHeaders);

    return $ArEmail;
  }

  /**
   * Check if the confirmation link is valid
   *
   * @param   String  $StVCode
   *
   * @return  Boolean   -
   */
  public function checkConfirmationLink($StEmail,$StKey) {
    $StSQL = "
SELECT
  IDUser
FROM
  " . DBPREFIX . "User
WHERE
  StEmail = '$StEmail'";

    $this->execSQL($StSQL);
    $CodCLi = $this->getResult("num");
    if (count($CodCLi[0][0] <= 0))
      throw new ErrorHandler(EXC_INV_EMAIL);

    $StVerif = md5($StEmail . $CodCLi[0][0] . date('mdY',time()) . 'cH@v3¬r0c@$&nHa');

    if ($StVerif != $StKey)
      throw new ErrorHandler(EXC_INV_KEY);
  }

 /**
  * Change the password
  *
  * @param   str   $StPwd      New Password
  * @param   str   $StEmail    User Email
  * @param   int   $ItCodHash  Hash Code
  */
  public function changePassword($StPwd,$StEmail,$StHash) {
    $StSQL = "
UPDATE
  " . DBPREFIX . "User
SET
  StPassword = '$StPwd',
  StHash = '$StHash'
WHERE
  StEmail = '$StEmail'";

    $this->setQuery($StSQL);
    $this->commit();

    $ItAffected = $this->getAffectedRows();
    if ($ItAffected >= 0) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Insert a new User (a client or a supporter) in DB
   *
   * @param array   $ArInsert
   *
   * @return array  $ArInsert  Array with all fileds inserted and its new id
   *
   * @author Mario Vítor <mario[at]digirati.com.br>
   */
  private function insertUser( $ArInsert ){

    if ( !is_array( $ArInsert ) || count( $ArInsert ) == 0 )
    	throw new ErrorHandler (EXC_USER_INSERT);

		$ArFields = array_keys( $ArInsert );	$ArFields[] = 'StHash';

    $ArNecessary = array( "StName", "StEmail", "StPassword" );

    #
    # filtering necessary fields
    #
    $BoInsert = true;
    foreach ( $ArNecessary as $StNecessary ){
  		if ( !array_key_exists( $StNecessary, $ArInsert ) ) {
  			$BoInsert = false; break;
  		}
  	}
  	if ( !$BoInsert ) {
  		throw new ErrorHandler (EXC_USER_INSERT);
  	}

		#
    # get hash with password given
    #
		$ArDataHashPwd = $this->generateHash( $ArInsert['StPassword'] );
    $ArInsert['StPassword'] = $ArDataHashPwd['cryptPwd'];
    $ArInsert['StHash'] = $ArDataHashPwd['codHash'];

    $StTableName = DBPREFIX . 'User';
    #
    # adding all in User Table
    #
    $ItAffected = $this->insertIntoTable( $StTableName, $ArFields, array($ArInsert) );
    if ( !$ItAffected ) {
      throw new ErrorHandler (EXC_USER_INSERT);
    } else {
      $ArInsert['IDUser'] = $this->getID();
    }

    #
    # return array with true insert
    #
    return  $ArInsert ;
  }

  /**
   * Inserts a Client User in DB
   *
   * @param array $ArInsert
   *
   * @return array $ArUser  All fields inserted
   *
   * @author Mario Vítor <mario[at]digirati.com.br>
   */
  public function insertClient( $ArInsert	){

  	#
  	# First of all, insert as a user
  	#
  	$ArUser = $this->insertUser( $ArInsert );

  	$ArFields = array(	"IDUser"	);
  	$ArClient = array(  "IDUser" => $ArUser['IDUser']  );
  	$StTableName = DBPREFIX . 'Client';

  	$ItAffected = $this->insertIntoTable( $StTableName, $ArFields, array( $ArClient ) );
    if ( !$ItAffected ) {
      throw new ErrorHandler (EXC_USER_INSERT);
    } else {
      $ArUser['IDClient'] = $this->getID();
    }

    return $ArUser;

  }

  /**
   * Inser a new Supporter on DB
   *
   * @param array $ArInsert
   * @param array $IDDepto
   * @param array $IDUnit
   *
   * @return array  $ArInsert  Array with all fileds inserted and its new id
   *
   * @author Mario Vítor <mario[at]digirati.com.br>
   */
  public function insertSupporter( $ArInsert, $IDUnit, $IDDepto ) {

  	#
  	# Just to validate
  	#
  	if ( is_null($IDDepto) || is_null($IDUnit) ) {
  		throw new ErrorHandler (EXC_USER_INSERT);
  	}
    #
  	# First of all, insert as a user
  	#
  	$ArUser = $this->insertUser( $ArInsert );

  	$ArFields = array(	"IDUnit", "IDUser"	);
  	$ArSupporter = array(
  		"IDUnit" => $IDUnit,
  		"IDUser" => $ArUser['IDUser']
		);
  	$StTableName = DBPREFIX . 'Supporter';

  	$ItAffected = $this->insertIntoTable( $StTableName, $ArFields, array( $ArSupporter ) );
    if ( !$ItAffected ) {
      throw new ErrorHandler (EXC_USER_INSERT);
    } else {
      $ArUser['IDSupporter'] = $this->getID();
    }

    #
    # Relating this new supporter in his department
    #
    $this->insertSupporterInDepartment( $ArUser['IDSupporter'], $IDDepto );
    $ArUser['IDUnit'] = $IDUnit;	$ArUser['IDDepartment'] = $IDDepto;

    return $ArUser;

  }

  /**
   * It does the relation between a supporter and his department
   *
   * @param integer $IDSupporter
   * @param integer $IDDepto
   *
   * @return VOID
   *
   * @author Mario Vítor <mario[at]digirati.com.br>
   */
  public function insertSupporterInDepartment ( $IDSupporter, $IDDepto ){

  	if ( is_null( $IDSupporter )  ||  is_null( $IDDepto ) ) {
    	throw new ErrorHandler (EXC_USER_INSERT);
  	}

    $ArFields = array( "IDDepartment", "IDSupporter" );
    $StTableName = DBPREFIX . 'DepartmentSupporter';
  	$ArToInsert = array( $IDDepto, $IDSupporter );
  	$ItAffected = $this->insertIntoTable( $StTableName, $ArFields, array($ArToInsert) );
  	if ( !$ItAffected ) {
      throw new ErrorHandler (EXC_USER_INSERT);
  	}

  }

  /**
   * Update a User on DB
   *
   * @param array    $ArData
   * @param integer  $IDUser
   *
   * @return $ArData  Array contains all updated datas
   *
   * @author Mario Vítor <mario[at]digirati.com.br>
   */
  public function updateUser($ArData, $IDUser){

    if (!is_array($ArData) || !isset($IDUser)){
    	throw new ErrorHandler (EXC_USER_UPDATE);
    }

		$StTableName = DBPREFIX . 'User';
  	$StCondition = 'IDUser = "' . $IDUser . '"';

    #
    # Getting a new encrypted password and the hash that encrypted it
    #
    if (isset( $ArData['StPassword'])) {
    	$ArDataHashPwd = self::generateHash( $ArData['StPassword'] );
		  $ArData['StHash'] = $ArDataHashPwd['codHash'];
		  $ArData['StPassword'] = $ArDataHashPwd['cryptPwd'];
    }

    $ItAffected = $this->updateTable($StTableName, $ArData , $StCondition, 1);

		return $ItAffected;

  }

  public function updateSupporter( $ArData, $IDSupporter, $IDUnit, $IDDepartment ) {

  	if ( !is_array($ArData) || is_null($IDSupporter) || is_null($IDDepartment) || is_null($IDUnit) ){
    	throw new ErrorHandler (EXC_USER_UPDATE);
    }

    #
    # the IDUser of this IDSupporter
    #
    $StSQL = "
SELECT
	S.IDUser
FROM
	".DBPREFIX."Supporter S
WHERE
	S.IDSupporter = ". $IDSupporter ;
    $this->execSQL($StSQL);		$IDUser = $this->getResult("string");
    $IDUser = $IDUser[0]['IDUser'];

    #
    # updating as a user on first
    #
    $ArData = $this->updateUser( $ArData, $IDUser );

		#
    # updating as a supporter
    #
    $StTableName = DBPREFIX . 'Supporter';
  	$StCondition = 'IDSupporter = "' . $IDSupporter . '"';
  	$ArUpdateSupporter = array(	"IDUnit" => $IDUnit	);
  	$ItAffected = $this->updateTable( $StTableName, $ArUpdateSupporter , $StCondition );
    if ( ! $ItAffected ) {
      throw new ErrorHandler (EXC_USER_UPDATE);
    } else {
    	$ArData['IDSupporter'] = $IDSupporter;
    }

    #
    # updating the supporter's department
    #
    $StTableName = DBPREFIX . 'DepartmentSupporter';
  	$StCondition = 'IDSupporter = "' . $IDSupporter . '"';
  	$ArUpdateDepartmentSupporter = array( "IDDepartment" => $IDDepartment );
  	$ItAffected = $this->updateTable( $StTableName, $ArUpdateDepartmentSupporter , $StCondition );
    if ( ! $ItAffected ) {
      throw new ErrorHandler (EXC_USER_UPDATE);
    } else {
    	$ArData['IDDepartment'] = $IDDepartment;
    }

    return $ArData;

  }

  /**
   * Remove a Client from DB
   *
   * @param array $ArDelete
   *
   * @return array ( 'sucess' => array sucess , 'error' => array error )
   *
   * @author Mario Vítor <mario[at]digirati.com.br>
   */
  //////////////// FIXME
  public function deleteClient( $IDUser, $StTypeClient = "" ){

    if ( !isset( $IDUser ) || empty( $StTypeClient ) )
      throw new ErrorHandler (EXC_USER_DELETE);

    switch ($StTypeClient) {
    	case "user":
    		$ArTableName = array( DBPREFIX . 'User' );
  			$StCondition = 'IDUser = "' . $IDUser .'"';
  		break;

    	case "supporter":
    		$StCondition = 'IDSupporter = "' . $IDUser .'"';
    		$this->updateTable( DBPREFIX . 'Message', array( "IDSupporter" => 0 ) , $StCondition );
    		$ArTableName = array(
    			DBPREFIX . 'DepartmentSupporter', ### remove it from his department
    			DBPREFIX . 'Supporter'
    		);
    	break;

    	default:
    		throw new ErrorHandler (EXC_USER_DELETE);
  		break;
    }

    foreach ( $ArTableName  as $StTableName) {
		  $ItAffected = $this->deleteFromTable( $StTableName, $StCondition );
		  if ( !$ItAffected ) {
		    throw new ErrorHandler (EXC_USER_DELETE);
		  }
    }

    return $IDUser;
  }

  /**
   * get foreign user data
   *
   * @param string $StEmail
   * @return array
   */
   public function getForeignUserData($StEmail) {
      global $UserFields;
      $StFields = implode(', ',$UserFields);

      $UserDB = new DBHandler(true);

      $StSQL = "
SELECT
  $StFields
FROM
  " . USERDBTABLE . "
WHERE
  {$UserFields['StEmail']} = '$StEmail'";

      $UserDB->execSQL($StSQL);
      $ArUser = $UserDB->getResult("string");

      if (count($ArUser) <= 0) {
        throw new ErrorHandler(EXC_USER_WRONGUSER);
      }

      $NewUserFields = array_flip($UserFields);
      $ArUserData = array();
      foreach ($ArUser[0] as $Key=>$Value) {
        $Field = $NewUserFields[$Key];
        $ArUserData[$Field] = $Value;
      }

      return $ArUserData;
   }

  /**
   * get user data, handling importation, after being included
   *
   * @param int $IDUser
   */
  public function getUserData($IDUser) {
    global $UserFields;

    $UserDB = new DBHandler(true);
    $StFields = implode(', ',$UserFields);

    if (ISEXTERNAL == 0) {
      $StSQL = "
SELECT
  $StFields
FROM
  " . DBPREFIX . "User
WHERE
  IDUser = '$IDUser'";

      $UserDB->execSQL($StSQL);
      $ArUser = $UserDB->getResult("string");

    } else {
      $StSQL = "
SELECT
  IDExternalUser
FROM
  " . DBPREFIX . "User
WHERE
  IDUser = '$IDUser'";

      $UserDB->execSQL($StSQL);
      $ArExternalUser = $UserDB->getResult("string");
      $IDExternalUser = $ArExternalUser[0]['IDExternalUser'];

      $StSQL = "
SELECT
  $StFields
FROM
  " . USERDBTABLE . "
WHERE
  {$UserFields['IDExternalUser']} = '$IDExternalUser'";

      $UserDB->execSQL($StSQL);
      $ArUser = $UserDB->getResult("string");
    }

    if (count($ArUser) <= 0) {
      throw new ErrorHandler(EXC_USER_WRONGUSER);
    }

    return $ArUser[0];
  }

  /**
   * generates a random password
   *
   * @return string
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function generatePassword() {
    $Password = '';
    $Chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $Len = mt_rand(6,8);
    $Aux = substr(str_shuffle($Chars),0,$Len);
    for ($i=0;$i<=$Len;$i++) {
      $Char = substr($Aux,$i,1);
      if (mt_rand(1,2) % 2 == 0) {
        $Password .= strtoupper($Char);
      } else {
        $Password .= $Char;
      }
    }

    return $Password;
  }

 /**
   * List all supporters by ticket
   *
   * @return $ArSupporters
   *
   * @author Mario Vitor <mario@digirati.com.br>
   */
  public function listSupporters($IDTicket){

    $ArSupporters = $ArSupporter1 = $ArSupporter2 = array();

  	$StSQL = '
SELECT
	S.IDSupporter, U.StName
FROM
	'. DBPREFIX .'User U
  LEFT JOIN ' . DBPREFIX . 'Supporter S ON (S.IDUser = U.IDUser)
  LEFT JOIN ' . DBPREFIX . 'TicketSupporter TS ON(S.IDSupporter = TS.IDSupporter)
  LEFT JOIN ' . DBPREFIX . 'Ticket T ON(TS.IDTicket = T.IDTicket)
WHERE
  T.IDTicket = ' . $IDTicket . '
OR
  U.IDUser = 0
';

  	$this->execSQL($StSQL);
    $ArSupporter1 = $this->getResult("string");

    $StSQL = '
SELECT
	S.IDSupporter, U.StName
FROM
	'. DBPREFIX .'User U
  RIGHT JOIN ' . DBPREFIX . 'Supporter S ON (S.IDUser = U.IDUser)
  LEFT JOIN ' . DBPREFIX . 'DepartmentSupporter DS ON(DS.IDSupporter = DS.IDSupporter)
  LEFT JOIN ' . DBPREFIX . 'Department D ON(DS.IDDepartment = D.IDDepartment)
  LEFT JOIN ' . DBPREFIX . 'TicketDepartment TD ON(D.IDDepartment = TD.IDDepartment)
  LEFT JOIN ' . DBPREFIX . 'Ticket T ON(TD.IDTicket = T.IDTicket)
WHERE
  T.IDTicket = ' . $IDTicket . '
GROUP BY
  S.IDSupporter
';

    $this->execSQL($StSQL);
    $ArSupporter2 = $this->getResult("string");

    if (! empty($ArSupporter1)) {
    	foreach ($ArSupporter1 as $ArField) {
    	  $ArSupporters[$ArField['IDSupporter']] = $ArField['StName'];
    	}
    }

    if (! empty($ArSupporter2)) {
    	foreach ($ArSupporter2 as $ArField) {
    	  $ArSupporters[$ArField['IDSupporter']] = $ArField['StName'];
    	}
    }

    return $ArSupporters;
  }

}

?>