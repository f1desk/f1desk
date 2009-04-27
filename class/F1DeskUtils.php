<?php
abstract class F1DeskUtils {

	private static $DBHandler;
	public static $CurrentPage = '';

  /**
   * Get one DBHandler's class instance
   *
   * @return unknown
   */
	private static function getDBinstance(){
		if ( ! self::$DBHandler instanceof DBHandler ) {
			self::$DBHandler = new DBHandler();
		}
	}

	/**
	 * outputs the right page, handling templates
	 *
	 * @return bool
	 *
	 * @author Dimitri Lameri <dimitri@digirati.com.br>
	 */
	public static function showPage($StPage = '') {
	  $StPage = preg_replace('/[^A-Z0-9]*/i','',$StPage);

	  if ( !empty($StPage) && file_exists(ABSTEMPLATEDIR . $StPage . '.php') )  {
	    self::$CurrentPage = $StPage;
	    require_once(ABSTEMPLATEDIR . $StPage . '.php');
	  }else if ($StPage = getSessionProp( 'lastPage' )) {
	    self::$CurrentPage = $StPage;
	    require_once(ABSTEMPLATEDIR . $StPage . '.php');
	    unsetSessionProp('lastPage');
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
		$ArMenu = array();  $Dom = new DOMDocument();
    $Dom->load( ABSTEMPLATEDIR . 'option.xml');
		$ObMenu = $Dom->getElementsByTagName('menu_tabs');
		foreach ( $ObMenu as $Item ){
			if ( $StPage == $Item->getAttribute('xml:id') ) $StCurrent = "current";
			else $StCurrent = "";
			$ArMenu[] = array(
				"Link" => $Item->getAttribute('xml:id'),
				"Name" => $Item->nodeValue,
				"Current" => $StCurrent
			);
		}
		
		return $ArMenu;
	}

  /**
	 * Checks if the user is a supporter
	 *
	 * @return unknown
	 */
	public static function IsSupporter() {
	  $isSupporter = getSessionProp('isSupporter');
	  return ($isSupporter && $isSupporter == 'true');
	}

	/**
   * Create a new category
   *
   * @param string  $StName category name
   * @return integer affected rows
   */
  public static function createCategory($StName) {
    $StTblName = DBPREFIX . 'Category';
    $ArFields = array('StCategory');
    $ArValues = array($StName);
    self::getDBinstance();
    $ItReturn = self::$DBHandler->insertIntoTable($StTblName,$ArFields,$ArValues);

    return $ItReturn;
  }

  /**
   * Create a new priority
   *
   * @param string  $StName priority name
   * @return integer affected rows
   */
  public static function createPriority($StName) {
    $StTblName = DBPREFIX . 'Priority';
    $ArFields = array('StPriority');
    $ArValues = array($StName);
    self::getDBinstance();
    $ItReturn = self::$DBHandler->insertIntoTable($StTblName,$ArFields,$ArValues);

    return $ItReturn;
  }

  /**
   * Create a new Unit
   *
   * @param string  $StName  Unit's name
   * @param integer $ArPermissions  Array with the Unit's permissions
   * @return unknown
   */
  public static function createUnit($StName,$ArPermissions) {
    $StTblName = DBPREFIX . 'Unit';
    $ArFields = array_keys($ArPermissions);
    array_unshift($ArFields,'StUnit');
    array_unshift($ArPermissions, $StName);
    self::getDBinstance();
    $ItReturn = self::$DBHandler->insertIntoTable($StTblName,$ArFields,$ArPermissions);
    return $ItReturn;
  }

  /**
   * Edit a unit created
   *
   * @param int $IDUnit
   * @param array $ArData
   * @return int
   */
  public static function editUnit($IDUnit, $ArData){
    $StTableName = DBPREFIX . "Unit";
		$StCondition = "IDUnit = " . $IDUnit;
		self::getDBinstance();

		return self::$DBHandler->updateTable( $StTableName, $ArData, $StCondition );
  }

  /**
   * removes a Unit created
   *
   * @param integer $IDUnit
   * @return int / boll
   */
  public static function removeUnit($IDUnit){
    self::getDBinstance();
    $StTableName = DBPREFIX . 'Unit';
    $StCondition = 'IDUnit = '. $IDUnit;
    try {
      return self::$DBHandler->deleteFromTable($StTableName,$StCondition);
    } catch (Exception $e){
      return false;
    }
  }

  /**
   * creates a new department
   *
   * @param string $StName Department's name
   * @param string $StDescription Department's description
   * @param string $TxSign Department's sign
   * @param string $IDParent ID of parent department case is subdepartment
   * @return unknown
   */
  public static function createDepartment($StName, $StDescription, $TxSign ='', $IDParent = null) {
    $StTblName = DBPREFIX . 'Department';
    $ArFields = array('StDescription','StDepartment','TxSign');
    $ArValues = array($StDescription,$StName,$TxSign);
    self::getDBinstance();
    self::$DBHandler->insertIntoTable($StTblName,$ArFields,$ArValues);
    $IDDepartment = self::$DBHandler->getID();
    # if is a subdpartment
    if (! is_null($IDParent)) {
      $StTblName = DBPREFIX . 'SubDepartment';
      $ArFields = array('IDSubDepartment','IDDepartment');
      $ArValues = array($IDDepartment,$IDParent);
      self::$DBHandler->insertIntoTable($StTblName,$ArFields,$ArValues);
    }
    return $IDDepartment;
  }

  /**
   * Edits a department
   *
   * @param integer $IDDepartment
   * @param array $ArData
   * @return int
   */
  public static function editDepartment($IDDepartment, $ArData){
    $StTableName = DBPREFIX . "Department";
		$StCondition = "IDDepartment = " . $IDDepartment;
		self::getDBinstance();

		return self::$DBHandler->updateTable( $StTableName, $ArData, $StCondition );
  }

  /**
   * remove a department by its ID
   *
   * @param integer $IDDepartment
   * @return int
   */
  public static function removeDepartment ($IDDepartment) {
    self::getDBinstance();

    #
    # Do this department have any tickets? yes? =O  poor tickets...
    #
    $StTableName = DBPREFIX . 'TicketDepartment';
    $StCondition = 'IDDepartment = '. $IDDepartment;
    self::$DBHandler->deleteFromTable($StTableName,$StCondition);

    #
    # Or have any subdepartment...
    #
		$StTableName = DBPREFIX . 'SubDepartment';
		$StCondition = 'IDDepartment = ' . $IDDepartment .
		               ' OR IDSubDepartment = ' . $IDDepartment;
  	self::$DBHandler->deleteFromTable($StTableName,$StCondition);

    #
    # Or have any supporters...
    #
		$StTableName = DBPREFIX . 'DepartmentSupporter';
		$StCondition = 'IDDepartment = ' . $IDDepartment;
  	self::$DBHandler->deleteFromTable($StTableName,$StCondition);

    #
    # Or have any cannedResponses...
    #
		$StTableName = DBPREFIX . 'DepartmentCannedResponse';
		$StCondition = 'IDDepartment = ' . $IDDepartment;
  	self::$DBHandler->deleteFromTable($StTableName,$StCondition);

		$StTableName = DBPREFIX . 'Department';
		$StCondition = 'IDDepartment = ' . $IDDepartment;
    return self::$DBHandler->deleteFromTable($StTableName,$StCondition);

  }

  /**
   * Create a new Rate
   *
   * @param string $StName
   * @return integer  affected rows
   */
  public static function createRate($StName) {
    $StTblName = DBPREFIX . 'Rate';
    $ArFields = array('StRate');
    $ArValues = array($StName);
    self::getDBinstance();
    $Itreturn = self::$DBHandler->insertIntoTable($StTblName,$ArFields,$ArValues);

    return $Itreturn;
  }

  /**
   * Creates a new Ticket type
   *
   * @param string $StType
   * @return integer  affected rows
   */
  public static function createTicketType($StType){
    $StTblName = DBPREFIX . 'Type';
    $ArFields = array('StType');
    $ArValues = array($StType);
    self::getDBinstance();
    $Itreturn = self::$DBHandler->insertIntoTable($StTblName,$ArFields,$ArValues);

    return $Itreturn;
  }

  /**
   * list the departments of an user
   *
   * @return array
   */
  public static function getUserDepartments() {
    $ArDepartment = array();

    $ArDepartment['opened'] = OPENEDCALLS;
  	$ArDepartment['closed'] = CLOSEDCALLS;

  	return $ArDepartment;
  }

  /**
   * get all departmentes
   *
   * @param int $IDUser
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public static function getDepartments( $IDSupporter ) {
    $ArDepartments = array();

    $StSQL = '
SELECT
	D.IDDepartment, D.StDepartment
FROM
	' . DBPREFIX . 'Department D
  LEFT JOIN ' . DBPREFIX . 'DepartmentSupporter DS ON (DS.IDDepartment = D.IDDepartment)
  LEFT JOIN ' . DBPREFIX . 'Supporter S ON (S.IDSupporter = DS.IDSupporter)
WHERE
  S.IDSupporter = ' . $IDSupporter . '
ORDER BY
	D.StDepartment';

  	self::getDBinstance();
  	self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult("string");

    foreach ($ArResult as $ArQuery){
    	$ArDepartments[$ArQuery['IDDepartment']] = $ArQuery['StDepartment'];
    }

    $ArDepartments['single'] = DEPT_SINGLE;
    $ArDepartments['mine'] = DEPT_MINE;
    $ArDepartments['bookmark'] = DEPT_BOOKMARK;
    $ArDepartments['ignored'] = DEPT_IGNORED;

    return $ArDepartments;
  }

	/**
   * List all existances departments and subdepartments
   *
   * @param int IDUser
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public static function getSubDepartments( $IDSupporter ){

    #
    # Getting who is Department and who is SubDepartment
    #
    $StSQL = '
SELECT
  D.IDDepartment, GROUP_CONCAT(SD.IDSubDepartment) as IDSubDepartments
FROM
	'.DBPREFIX.'SubDepartment SD
  LEFT JOIN '.DBPREFIX.'Department D ON (SD.IDDepartment = D.IDDepartment)
  LEFT JOIN '.DBPREFIX.'DepartmentSupporter DS ON (DS.IDDepartment = D.IDDepartment)
  LEFT JOIN '.DBPREFIX."Supporter S ON (S.IDSupporter = DS.IDSupporter)
WHERE
  S.IDSupporter = $IDSupporter
GROUP BY
  SD.IDDepartment";

    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArSubDepartments = array();
    $ArResult = self::$DBHandler->getResult("string");

    foreach ( $ArResult as $ArDepartment ) {
      $ArSubSeparation = explode(',', $ArDepartment[ 'IDSubDepartments' ]);
      $ArSubDepartments[$ArDepartment['IDDepartment']] = $ArSubSeparation;
    }

    return $ArSubDepartments;

  }

  /**
   * List all existances units
   *
   * @return $ArUnits < array >
   *
   * @author Mario Vitor <mario@digirati.com.br>
   */
  public static function listUnits(){

  	$StSQL = "
SELECT
	*
FROM
	" . DBPREFIX . "Unit";

  	self::getDBinstance();
  	self::$DBHandler->execSQL($StSQL);
    $ArUnits = self::$DBHandler->getResult("string");

    return $ArUnits;

  }

  /**
   * List gereral options
   *
   * @return array
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public static function listGeneralOptions(){

    $ArGeneralOptions = array(
      'title' => '',
      'date_format' => '',
      'time_format' => '',
      'datetime_format' => '',
      'upload_max_size' => ''
    );

    foreach ($ArGeneralOptions as $NodeName => &$Option) {
      $Option = getOption($NodeName);
    }

    return $ArGeneralOptions;
  }

  /**
   * Lists All Categories
   *
   * @return array
   */
  public static function listCategories() {
    $StSQL = "
SELECT
  *
FROM
" . DBPREFIX . "Category";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArCategories = self::$DBHandler->getresult('string');
    $ArReturn = array();
    for($i=0; $i < count($ArCategories); $i++ ) {
      $ArReturn [ $ArCategories[$i]['IDCategory'] ] = $ArCategories[$i]['StCategory'];
    }
    return $ArReturn;
  }

  /**
   * Lists all priorities
   *
   * @return unknown
   */
  public static function listPriorities() {
   $StSQL = "
SELECT
  *
FROM
". DBPREFIX . "Priority";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArPriority = self::$DBHandler->getResult('string');
    $ArReturn = array();
    for($i=0; $i < count($ArPriority); $i++ ) {
      $ArReturn [ $ArPriority[$i]['IDPriority'] ] = $ArPriority[$i]['StPriority'];
    }
    return $ArReturn;
  }

  /**
   * Lists all rates availables
   *
   * @return array
   */
  public static function listRate(){
    $StSQL = "
SELECT
  *
FROM
" . DBPREFIX . "Rate";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    self::$DBHandler->getResult('string');

    for($i=0; $i < count($ArRates); $i++ ) {
      $ArReturn [ $ArRates[$i]['IDRate'] ] = $ArRates[$i]['StRate'];
    }
    return $ArReturn;
  }

  /**
   * Lists all ticket types availables
   *
   * @return array
   */
  public static function listTicketTypes(){
    $StSQL = "
SELECT
  *
FROM
" . DBPREFIX . "Type";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);

    $ArTypes = self::$DBHandler->getResult('string');
    $ArReturn = array();

    for($i=0; $i < count($ArTypes); $i++ ) {
      $ArReturn[ $ArTypes[$i]['IDType'] ] = $ArTypes[$i]['StType'];
    }

    return $ArReturn;
  }

  /**
   * formats the date to the choosen one
   *
   * @param string $StFormat
   * @param date $Date
   *
   * @author Dimitri Lameri <dimitri@digirati.com.br>
   */
  public static function formatDate($StFormat, $Date = false) {
    if ($Date === false){
      $Date = time();
    }
    return date(getOption($StFormat),strtotime($Date));
  }

  /**
   * sorts an array using the id
   *
   * @param array $Array
   * @param int $ID
   * @return array
   */
  public static function sortByID($Array, $ID) {
    $sorttedArray = array();

    foreach ($Array as $Line) {
      $sorttedArray[$Line[$ID]] = $Line;
    }

    ksort($sorttedArray);

    return $sorttedArray;
  }

  /**
   * Check if the supporter given have permisssion about the action given
   *
   * @param str $StAction
   * @param int $IDSupporter
   * @return boolean
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public static function getPermission($StAction, $IDSupporter) {
    self::getDBinstance();
    $StSQL = "
SELECT
  $StAction
FROM
  " . DBPREFIX . "Unit U
LEFT JOIN
  " . DBPREFIX . "Supporter S ON (S.IDUnit = U.IDUnit)
WHERE
  S.IDSupporter = $IDSupporter";
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');
    if(isset($ArResult[0][$StAction]))
      return ($ArResult[0][$StAction] == 0) ? false : true;
    else
      return false;
  }

  /**
   * Get data from User Table with a Supporter/Client ID given
   *
   * @param int  $ID  Supporter/Client ID
   * @param int  $ItType  0 = Supporter, 1 = Client
   * @return array User data
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public static function getUserData($ID,$ItType = 0) {
    $StField = ($ItType == 0) ? 'IDSupporter' : 'IDClient';
    $StTable = ($ItType == 0) ? 'Supporter' : 'Client';
    $StSQL = "
  SELECT
    U.*
  FROM
    " . DBPREFIX . "User U
  LEFT JOIN
    ". DBPREFIX ."$StTable ON (U.IDUser = ". DBPREFIX ."$StTable.IDUser)
  WHERE
    ". DBPREFIX ."$StTable.$StField = $ID ";

    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');
    return array_shift($ArResult);
  }

	public static function updateUserData( $IDUser, $ArData ){
  	$StTableName = DBPREFIX . "User";
  	$StCondition = "IDUser = $IDUser";
  	self::getDBinstance();

  	return self::$DBHandler->updateTable($StTableName, $ArData, $StCondition);
  }

  public static function listCannedResponses($IDSupporter) {

    #
    # get Supporter's Departments
    #
    $ArDepartments = self::getDepartments($IDSupporter);

    $ArIDs = array_keys($ArDepartments);

    #
    # get only the real departmets, excluding ignored and singles
    #
    foreach ($ArIDs as $Key => $Value) {
      if (! preg_match('/[0-9]+/', $Value)) {
        unset($ArIDs[$Key]);
      }
      $StIDs = implode(',',$ArIDs);
    }

    $StSQL = "
SELECT
  C.*
FROM
  " . DBPREFIX . "Supporter S
LEFT JOIN
  " . DBPREFIX . "CannedResponse C ON(C.IDSupporter = S.IDSupporter)
LEFT JOIN
  " . DBPREFIX . "DepartmentCannedResponse DCR ON(DCR.IDCannedResponse = C.IDCannedResponse)
LEFT JOIN
  " . DBPREFIX . "Department D ON(DCR.IDDepartment = D.IDDepartment)
WHERE
  S.IDSupporter = $IDSupporter
OR
  D.IDDepartment IN ($StIDs)";

    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');
    return $ArResult;
  }

  public static function createCannedResponse ( $ArData ){
  	$ArFields = array_keys($ArData);
  	$StTableName = DBPREFIX . 'CannedResponse';
  	self::getDBinstance();

  	if ( self::$DBHandler->insertIntoTable($StTableName, $ArFields, array($ArData)) ) {
  		return self::$DBHandler->getID();
  	} else {
  		return false;
  	}

  }

  public static function editCannedResponse( $IDCannedResponse, $ArData ){
		$StTableName = DBPREFIX . 'CannedResponse';
		$StCondition = 'IDCannedResponse = ' . $IDCannedResponse;
		self::getDBinstance();

		return self::$DBHandler->updateTable( $StTableName, $ArData, $StCondition );
  }

  public static function removeCannedResponse ( $IDCannedResponse ) {
		$StTableName = DBPREFIX . 'CannedResponse';
		$StCondition = 'IDCannedResponse = ' . $IDCannedResponse;
  	self::getDBinstance();

 		return self::$DBHandler->deleteFromTable($StTableName,$StCondition);
  }

  public static function listNotes( $IDSupporter ){
  	$StSQL = "
SELECT N.*
FROM
	". DBPREFIX ."Note N
WHERE
	N.IDSupporter = $IDSupporter
  	";

  	self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');
    return $ArResult;
  }

  public static function createNote( $ArData ){
  	$ArFields = array_keys($ArData);
  	$StTableName = DBPREFIX . "Note";
  	self::getDBinstance();

  	if ( self::$DBHandler->insertIntoTable($StTableName, $ArFields, array($ArData)) ) {
  		return self::$DBHandler->getID();
  	} else {
  		return false;
  	}
  }

  public static function editNote( $IDNote, $ArData ){
		$StTableName = DBPREFIX . "Note";
		$StCondition = "IDNote = " . $IDNote;
		self::getDBinstance();

		return self::$DBHandler->updateTable( $StTableName, $ArData, $StCondition );
  }

  public static function removeNote ( $IDNote ) {
		$StTableName = DBPREFIX . 'Note';
		$StCondition = 'IDNote = ' . $IDNote;
  	self::getDBinstance();

 		return self::$DBHandler->deleteFromTable($StTableName,$StCondition, 1);
  }

  public static function listSupporterBookmark( $IDSupporter ){
  	$StSQL = "
SELECT T.StTitle, B.IDSupporter, B.IDTicket
FROM
	". DBPREFIX ."Bookmark B
	LEFT JOIN	". DBPREFIX ."Ticket T
		on ( T.IDTicket = B.IDTicket )
WHERE
	B.IDSupporter = $IDSupporter
  	";

  	self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');
    return $ArResult;
  }

  public static function toTMP($StIncome,$StMode = 'path') {
    $tmpFile = tmpfile();
    if ($StMode == 'path') {
      $Content = file_get_contents($StIncome);
    } else {
      $Content = $StIncome;
    }
    fwrite($tmpFile,$Content);
    return $tmpFile;
  }

  public static function getUserHeaderSign($IDUser) {
    self::getDBinstance();
    $StSQL = '
SELECT
  U.TxHeader, U.TxSign
FROM
  ' . DBPREFIX . "User U
WHERE
  U.IDUser = $IDUser";
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');
    return array_shift($ArResult);
  }

  public static function isBookmarked($IDSupporter,$IDTicket) {
    $StSQL = '
SELECT
  COUNT(*)
FROM
' . DBPREFIX . "Bookmark
WHERE
  IDSupporter = $IDSupporter
AND
  IDTicket = $IDTicket";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArReturn = self::$DBHandler->getResult('num');

    return ($ArReturn[0][0] > 0) ? true : false;
  }

  public static function isIgnored($IDSupporter,$IDTicket) {
    $StSQL = '
SELECT
  COUNT(*)
FROM
' . DBPREFIX . "Ignored
WHERE
  IDSupporter = $IDSupporter
AND
  IDTicket = $IDTicket";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ItAffected = self::$DBHandler->getNumRows();
    $ArReturn = self::$DBHandler->getResult('num');

    return ($ArReturn[0][0] > 0) ? true : false;
  }

  public static function isAttached($IDTicket, $IDAttach) {
    $StSQL = '
SELECT
  COUNT(*)
FROM
' . DBPREFIX . "AttachedTicket
WHERE
  IDTicket = $IDTicket
AND
  IDAttachedTicket = $IDAttach";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ItAffected = self::$DBHandler->getNumRows();
    $ArReturn = self::$DBHandler->getResult('num');

    return ($ArReturn[0][0] > 0) ? true : false;
  }

  /**
   * Get all supporters
   *
   */
  public static function getAllSupporters() {
    $StSQL = '
SELECT
  S.IDSupporter, U.StName
FROM
'.DBPREFIX.'Supporter S
LEFT JOIN '.DBPREFIX.'User U ON (S.IDUser = U.IDUser)
ORDER BY S.IDSupporter';
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');
    return $ArResult;
  }

  /**
   * Check if the system have to notify the users when a new message is sent
   *
   * @param int $IDUser
   * @return boolean
   */
  public static function notify($IDUser) {
    $StSQL = '
SELECT
  BoNotify
FROM
  '.DBPREFIX."User
WHERE
  IDUser = $IDUser";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('num');
    return (boolean)$ArResult[0][0];
  }

  /**
   * get all suupporters of a specific department
   *
   * @param int $IDDepartment
   */
  public static function getDepartmentSupporters($IDDepartment) {
    $StSQL = '
SELECT
  U.*
FROM
  '.DBPREFIX.'User U
LEFT JOIN '.DBPREFIX.'Supporter S ON (U.IDUser = S.IDUser)
LEFT JOIN '.DBPREFIX.'DepartmentSupporter DS ON (S.IDSupporter = DS.IDSupporter)
LEFT JOIN '.DBPREFIX."Department D ON (D.IDDepartment = DS.IDDepartment)
WHERE
  D.IDDepartment = $IDDepartment";
    self::getDBinstance();
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');

    return $ArResult;
  }

  public static function getDepartmentsFormatted($IDSupporter) {
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
    return $ArFormatted;
  }

  /**
   * Return the non-internal departments or all departments
   *
   * @return Array
   *
   * @author Matheus Ashton <matheus@digirati.com.br>
   */
  public static function getPublicDepartments($BoPublic = true) {

    self::getDBinstance();
    $ArDepartments = array();

    if ($BoPublic !== true) {
      $StSQL = '
SELECT
  D.*
FROM
  '.DBPREFIX.'Department D
LEFT JOIN '.DBPREFIX.'SubDepartment SD ON (D.IDDepartment = SD.IDDepartment)
GROUP BY
  D.IDDepartment';
    } else  {
      $StSQL = '
SELECT
  D.*
FROM
  '.DBPREFIX.'Department D
LEFT JOIN '.DBPREFIX.'SubDepartment SD ON (D.IDDepartment = SD.IDDepartment)
WHERE
  BoInternal = 0
GROUP BY
  D.IDDepartment';
    }
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');

    foreach ($ArResult as $ArDepartment) {
      $ArDepartments[$ArDepartment['IDDepartment']] = $ArDepartment;
    }

    $StSQL = '
SELECT
  D.IDDepartment, D.StDescription, GROUP_CONCAT(SD.IDSubDepartment) as IDSubDepartments
FROM
	'.DBPREFIX.'SubDepartment SD
LEFT JOIN '.DBPREFIX.'Department D ON (SD.IDDepartment = D.IDDepartment)
LEFT JOIN '.DBPREFIX.'DepartmentSupporter DS ON (DS.IDDepartment = D.IDDepartment)
GROUP BY
  SD.IDDepartment';
    self::$DBHandler->execSQL($StSQL);
    $ArResult = self::$DBHandler->getResult('string');

    foreach ( $ArResult as $Department ) {
      $ArSubSeparation = explode(',', $Department[ 'IDSubDepartments' ]);
      $ArSubDepartments[$Department['IDDepartment']] = array_unique($ArSubSeparation);
    }

    foreach ($ArDepartments as $ArDepartment) {
      if (array_key_exists($ArDepartment['IDDepartment'],$ArSubDepartments)) {
        foreach ($ArSubDepartments as $IDParent => $ArSub) {
          if ($IDParent == $ArDepartment['IDDepartment']) {
            foreach ($ArSub as $IDSub){
              $ArDepartments[$ArDepartment['IDDepartment']]['SubDepartments'][$IDSub]['IDSub'] = $IDSub ;
              $ArDepartments[$ArDepartment['IDDepartment']]['SubDepartments'][$IDSub]['StSub'] = $ArDepartments[$IDSub]['StDepartment'];
              $ArDepartments[$ArDepartment['IDDepartment']]['SubDepartments'][$IDSub]['StSubDescription'] = $ArDepartments[$IDSub]['StDescription'];
              if (isset($ArDepartments[$IDSub]))
                unset($ArDepartments[$IDSub]);
            }
          }
        }
      }
    }

    return $ArDepartments;
  }
  
  public static function editOption($StOption, $StValue){
    return setOption($StOption, array('text'=>$StValue), 'name');
  }
  
  public static function getTemplates(){
    $DomTemplateNode = getOption('avail_templates', 'node');
    $StChoosenTemplate = $DomTemplateNode->item(0)->getAttribute('choosen');
    $DomAvailTemplates = $DomTemplateNode->item(0)->getElementsByTagName('template');
    $ArTemplates = array();
    foreach ($DomAvailTemplates as $elementTemplate) {
    	$ArTemplates[] = array(
        "StName" => $elementTemplate->nodeValue,
        "StPath" => $elementTemplate->getAttribute('path'),
        "StThumbnail" => $elementTemplate->getAttribute('thumbnail'),
        "StDescription" => $elementTemplate->getAttribute('description'),
        "BoSelected" => ($elementTemplate->nodeValue == $StChoosenTemplate)?true:false
    	);
    }
    return $ArTemplates;
  }
  
  public static function setCurrentTemplate($StName){
    $Dom = new DOMDocument();
    $Dom->load( INCLUDESDIR . '/option.xml');
    $Dom->formatOutput = true;
    $DomTemplateNode = $Dom->getElementsByTagName('avail_templates');
    $DomTemplateNode->item(0)->setAttribute('choosen', $StName);
    return ($Dom->save( INCLUDESDIR . '/option.xml'));
  }
  
  public static function createTemplate($StName, $StPath, $StThumbnail, $TxDescription){
    return createOption('avail_templates', 'template', $StName, array(
      'xml:id' => str_replace(' ','_',strtolower($StName)), 'path'=> $StPath, 
      'thumbnail' => $StThumbnail, 'description' => $TxDescription
    ));
  }
  
  public static function removeTemplate($StName){
    return removeOption(str_replace(' ', '_',strtolower($StName)), 'id');
  }
  
  public static function getLanguages(){
    $DomLanguageNode = getOption('avail_languages', 'node');
    $StChoosenLanguage = $DomLanguageNode->item(0)->getAttribute('choosen');
    $DomAvailLanguages = $DomLanguageNode->item(0)->getElementsByTagName('language');
    $ArLanguages = array();
    foreach ($DomAvailLanguages as $elementLanguage) {
    	$ArLanguages[] = array(
        "StPath" => $elementLanguage->nodeValue,
        "StTitle" => $elementLanguage->getAttribute('title'),
        "BoSelected" => ($elementLanguage->nodeValue == $StChoosenLanguage)?true:false
    	);
    }
    return $ArLanguages;
  }
  
  public static function editLanguage($StTitle, $StPath){
    return setOption($StPath,array(
      'text' => $StPath, 'title' => $StTitle, 'id' => strtolower($StPath)
    ),'id');
  }
  
  public static function removeLanguage($StPath){
    return removeOption(strtolower($StPath), 'id');
  }
  
  public static function setCurrentLanguage($StPath){
    $Dom = new DOMDocument();
    $Dom->load( INCLUDESDIR . '/option.xml');
    $Dom->formatOutput = true;
    $DomLanguageNode = $Dom->getElementsByTagName('avail_languages');
    $DomLanguageNode->item(0)->setAttribute('choosen', $StPath);
    return ($Dom->save( INCLUDESDIR . '/option.xml'));
  }
  
  public static function createLanguage($StTitle, $StPath){
    return createOption('avail_languages', 'language', $StPath, array(
      'xml:id' => strtolower($StPath), 'title' => $StTitle
    ));
  }
}
?>