<?php

require_once('../main.php');
require_once('../lang/pt_BR/lang.SearchHandler.php');

abstract class SearchHandler{
  private static $DBHandler;
//  private static $IDTicket;
  private static $BoSetDepartment = FALSE;
//  private static $IDSupporterTicket;
  private static $IDSupporterLogged = NULL;
  /*private static $DtStart;
  private static $DtEnd;*/
/*  private static $ItPage = NULL;
  private static $ItLimit = NULL;*/
  private static $ArStWord;
  private static $ArIDWord;
  private static $ArData;
//  private static $ArIDent;  
  private static $StSelectCustomField;
  private static $ArWhere;
  private static $StOrderBy;
  private static $StLimit;
  private static $StGroupBy;
  private static $StSQL;
  
  
  private static function DateValidate( $StDate, $StError = NULL ){
    if ( !empty($StDate) ){
      try {
        $Dt = new DateTime($StDate);
        $Dt = $Dt->format('Y-m-d');      
        $ArDt = explode( '-', $Dt);
        
        if (checkdate($ArDt[1], $ArDt[2], $ArDt[0])){
          $Dt = implode('-', $ArDt);
          if ( strtotime($Dt)>time() || strtotime($Dt) == false ){
            throw new errorHandler( sprintf(EXC_INVALID_BIGGEST_DT, $Dt) );
          }
          else{
            return $Dt;
          }
        }        
        throw new errorHandler( EXC_BAD_ARGUMENT );
      }
      catch ( ErrorException $e ){
        throw new errorHandler( sprintf($StError, $Dt ) );
      }
    }
    return (string)$StDate;   
  }
  
  private static function NumberValidate( $ItID, $StError = NULL, $BoPositive = false ){
    #
    ## if the argument has given but isnt a number
    #
    if ( !empty($ItID) && !is_numeric($ItID) ){
      if ( empty($StError)){
        $StError = EXC_BAD_ARGUMENT;
      }
      throw new errorHandler( sprintf($StError, $ItID) );
    }
    elseif (empty($ItID) && $BoPositive){
      throw new errorHandler( EXC_NUMBER_SHOULD_BE_BIGGER_THAN_ZERO );
    }
    return (int)$ItID;
  }
     
  private static function getDBinstance(){
		if ( ! self::$DBHandler instanceof DBHandler ) {
			self::$DBHandler = new DBHandler();
		}
	}
	
	#
	## Example: $ArFieldValue = array ('LastName'=>'Gomes', 'Occupation'=>'Programmer')
	#
	private static function setWhereCustomField( $ArFieldValue, $BoUser = TRUE ){
	  if ( empty($ArFieldValue) || !is_array($ArFieldValue)){
	    return NULL;
	  }
	  $StWhere = '';
	  
	  if ($BoUser){
	    $StAlias = 'U2';
	  }
	  else {
	    $StAlias = 'U1';
	  }
	  
	  #
	  ## User Table Alias 'U.' is not required in call time
	  #
	  foreach ($ArFieldValue as $StField => $StValue ){
	    $StField = addslashes($StField);
	    $StValue = addslashes($StValue);
	    $StWhere = " $StAlias.$StField = '$StValue' ";
	    self::ArWhereAdd($StWhere);
	  }	 
	}
	
	private static function setSelectCustomField( $ArField = array(), $BoUser = TRUE ){
	  if (empty($ArField) || !is_array($ArField)){
	    self::$StSelectCustomField = '';
	    return NULL;
	  }
	  
	  if ($BoUser){
	    $StAlias = 'U2';
	  }
	  else {
	    $StAlias = 'U1';
	  }	  
//	  (print_r($ArField,1));
	  foreach ($ArField as $key=>$StField){
	    $ArField[$key] = "$StAlias.$StField";
	  }
	  
	  $StSelectCustomField = implode(',', $ArField) . ',';	  
  	self::$StSelectCustomField = addslashes($StSelectCustomField);
	}
	
	private static function getWhere(){
	  if (empty(self::$ArWhere) || !is_array(self::$ArWhere)){
	    return '';
	  }
	  
	  if ( empty(self::$BoSetDepartment) ){
	    self::setDepartment(NULL);
	  }
	  $StWhere = " \n WHERE " . implode( "\n AND ", self::$ArWhere );
	  return $StWhere;
	}
	
	private static function getGroupBy(){
	  if (empty(self::$StGroupBy)){
	    return '';
	  }
	  else{
	    return addslashes(self::$StGroupBy);
	  }  
	}
	
	private static function getOrderBy(){
	  if (empty(self::$StOrderBy)){
	    return '';
	  }
	  else{
	    return addslashes(self::$StOrderBy);
	  }
	}
	
	private static function getLimit(){
	  if (empty(self::$StLimit)){
	    self::setLimit();
	  }
	  return self::$StLimit;
	}
	
	private static function ArWhereAdd( $StWhere, $BoFirst = FALSE ){
	  if (!empty($StWhere)){
   	  $ArWhere = (array)self::$ArWhere;
   	  if ($BoFirst){
   	    array_unshift($ArWhere, $StWhere);
   	  }
   	  else{
   	    $ArWhere[] = $StWhere;
   	  }
	    self::$ArWhere = $ArWhere;
	  }
	}
	
	private static function FieldTableValidate( $StTable ){
	  #
	  ## verify if the field exists and return the alias respective
	  #
	  if (empty($StTable)){
	    throw new errorHandler( EXC_BAD_ARGUMENT );
	  }
	  $ArSortTableFieldAlias = array( 'TICKET'=>'T', 'TICKETDEPARMENT'=>'TD', 'USER'=>'U', 'CATEGORY'=>'C', 'DEPARTMENT'=>'D');
    $StTable = strtoupper($StTable);
	  if ( key_exists( $StTable, $ArSortTableFieldAlias ) ){
	    return $ArSortTableFieldAlias[$StTable];
	  }
	  else{
	    throw new errorHandler( sprintf(EXC_TABLE_NOT_AVAILABLE, $StTable) );
	  }
	}
	
	public static function reset(){
    $ArSearchHandlerVar = array_keys(get_class_vars('SearchHandler'));    
    foreach ($ArSearchHandlerVar as $Var){
      self::$$Var = NULL;
    }
  }

	public static function setDepartment( $IDDepartment = NULL ){
	  $IDDepartment = self::NumberValidate($IDDepartment, EXC_INVALID_IDDEPARTMENT);
	  if ( !empty($IDDepartment) ){
  	  $StWhere = " TD.IDDepartment = $IDDepartment ";
	  } 
	  else{
	    if (empty(self::$IDSupporterLogged)){
	      throw new errorHandler( EXC_BAD_ARGUMENT . ' "IDSupporterLogged" ');
	    }
//	    die('aqui >> '. self::$IDSupporterLogged);
      $ArDepartment = F1DeskUtils::getDepartments(self::$IDSupporterLogged);
	    $ArDepartment = array_keys($ArDepartment);
//      print_R($ArDepartment);
      foreach ($ArDepartment as $StDepartment){
        if ( is_numeric($StDepartment) ){
          $ArIDDepartment[] = $StDepartment;
        }
      }
      unset($ArDepartment);
      $StInDepartment = implode(',', $ArIDDepartment); 
      $StWhere = " TD.IDepartment IN ( $StInDepartment ) ";
	  }
	  self::$BoSetDepartment = TRUE;
 	  self::ArWhereAdd($StWhere);
 	  print_R(self::$ArWhere);
	}
	
	public static function setLogged( $IDSupporterLogged ){
    #
    ## Here, $IDSupporterLogged is obrigatory
    #
    $IDSupporterLogged = empty($IDSupporterLogged) ? NULL : $IDSupporterLogged;
    
    if ( !is_numeric($IDSupporterLogged) ){
       throw new errorHandler( sprintf( EXC_INVALID_IDSUPPORTER, $IDSupporterLogged ) );         
    } 
    self::$IDSupporterLogged = $IDSupporterLogged;    
	}
	
	public static function setSupporter( $IDSupporterTicket = NULL ){
	  $IDSupporterTicket = self::NumberValidate($IDSupporterTicket, EXC_INVALID_IDSUPPORTER);
	  if ($IDSupporterTicket){
	    $StWhere = " T.IDSupporter = $IDSupporterTicket ";
	    self::ArWhereAdd($StWhere);
	  }
	}
	
	public static function setUser( $StUserTicket = NULL ){
	  if (empty($StUserTicket)){	    
	    return false;
	  }
	  $StUserTicket = addslashes($StUserTicket);
	  $StWhere = " U2.StName LIKE '$StUserTicket' ";
    self::ArWhereAdd($StWhere);
	}
	
	public static function setTicket( $IDTicket = NULL ){
	  $IDTicket = self::NumberValidate($IDTicket, EXC_INVALID_IDTICKET);
	  #
	  ## If IDTicket exists, it will go to first place in sql
	  #
	  if (!empty($IDTicket)){
  	  $StWhere = " T.IDTicket = $IDTicket ";
  	  self::ArWhereAdd( $StWhere, TRUE );
	  }
	}
	
	public static function setCategory( $IDCategory = NULL ){
	  $IDCategory = self::NumberValidate($IDCategory, EXC_INVALID_IDCATEGORY);
	  if (!empty($IDCategory)){
  	  $StWhere = " T.IDCategory = $IDCategory ";
  	  self::ArWhereAdd( $StWhere );
	  }
	}
   
	public static function setDtStartEnd( $DtStart = NULL, $DtEnd = NULL ){
      $DtStart = self::DateValidate($DtStart, EXC_INVALID_DTSTART);
      $DtEnd = self::DateValidate($DtEnd, EXC_INVALID_DTEND);
      $StWhere = '';
      if ( !empty($DtStart) && !empty($DtEnd)){
        #
        ## Change values if DtStart > DtEnd
        #
        if (strtotime($DtStart)>strtotime($DtEnd)){
          throw new errorHandler(EXC_DTSTART_BIGGER_THAN_DTEND);
        }
        $StWhere = " T.DtOpened >= '" . $DtStart . 
                   "' AND T.DtOpened <= '" . $DtEnd . "' " ;
      }
      elseif (!empty($DtStart)){
        $StWhere = " T.DtOpened >= '" . $DtStart . "' ";
      }
      elseif (!empty($DtEnd)){
        $StWhere = " T.DtOpened <= '" . $DtEnd . "' ";
      }
      else{
        return false;
      }
      self::ArWhereAdd( $StWhere, TRUE );      
	}
	# EX: array( 'Ticket'=> IDSupporter )	
	public static function setGroupBy( $ArGroupBy = array(), $ArHaving = NULL ){
	  if ( !empty($ArGroupBy) && is_array($ArGroupBy)){
	    $ArField = array_keys($ArGroupBy);
	    $StTableBy = $ArField[0]; unset($ArField);
	    $StAliasBy = self::FieldTableValidate($StTableBy);
	    $StHaving = '';
	    
	    if ( !empty($ArHaving) && is_array($ArHaving)){
	      if (empty($ArHaving['Alias'])){
  	      $ArField = array_keys($ArHaving);
  	      $StTable = $ArField[0]; unset($ArField);
  	      $StAlias = self::FieldTableValidate($StTable);
  	      $StAlias = "$StAlias.$StTable";
	      }
	      else{
	        $StAlias = $ArHaving['Alias'];
	      }
	      
	      if ( empty($ArHaving['Operator']) || count($ArHaving['Operator'])>4 ){
	        throw new Exception(EXC_BAD_ARGUMENT);
	      }
	      $StOperator = addslashes($ArHaving['Operator']);
	      $StValue = addslashes($ArHaving['Value']);
	      $StHaving = " HAVING $StAlias $StOperator $StValue ";
	    }
	    self::$StGroupBy = " GROUP BY $StAliasBy.$ArGroupBy[$StTableBy] $StHaving ";
	  }
	  else{
	    self::$StGroupBy = '';
	  }
	}
	# EX: array( 'Ticket'=> IDSupporter )	
	public static function setOrderBy( $ArOrderBy = array(), $BoAsc = TRUE ){
	  if ( !empty($ArOrderBy) && is_array($ArOrderBy)){
	    if (empty($BoAsc)){
	      $StSort = 'DESC';
	    }
	    else {
	      $StSort = 'ASC';
	    }
	    $ArField = array_keys($ArOrderBy);
	    $ArOrderByValidated = array();

	    foreach ((array)$ArField as $Table){
	      $StTableAlias = self::FieldTableValidate($Table);
	      $StField = addslashes($ArOrderBy[$Table]);	      
	      $ArOrderByValidated[] = "$StTableAlias.$StField";
	    }
	    $StOrderBy = implode(', ', $ArOrderByValidated);
	    if (!empty($StOrderBy)){
	      self::$StOrderBy = " ORDER BY $StOrderBy $StSort";
	    }
	  }
	  else {
	    self::$StOrderBy = '';
	  }
	}

	public static function setLimit( $ItLimit = 50, $ItPage = 1 ){
	  if ( !empty(self::$StLimit)){ 
	    self::$StLimit = NULL;
	  }
	  $ItLimit = self::NumberValidate($ItLimit, EXC_INVALID_LIMIT, TRUE);	  
	  $ItPage = self::NumberValidate($ItPage, EXC_INVALID_PAGE, TRUE);	  
    $ItStart = ($ItPage - 1)  * $ItLimit;

	  self::$StLimit = " LIMIT $ItStart, $ItLimit; ";  
	}	
	
	public static function setArWord( $ArWord = array() ){
	  self::$ArStWord = (array)$ArWord;
	}
  
  public static function Search(){
    $StSelectCustomField = self::$StSelectCustomField;
    $StWhere = self::getWhere();
    $StGroupBy = self::getGroupBy();
    $StOrderBy = self::getOrderBy();
    $StLimit = self::getLimit();
    
    print $SQL = 
    'SELECT ' . $StSelectCustomField .
    ' T.IDTicket, T.StTitle, T.DtOpened, U1.StName AS Supporter, U2.StName AS User, D.StDepartment, C.StCategory
     FROM
      Ticket AS T
      LEFT JOIN Supporter AS S ON ( S.IDSupporter = T.IDSupporter )
      LEFT JOIN User AS U1 ON (S.IDUser = U1.IDUser)
      LEFT JOIN User AS U2 ON (U2.IDUser = T.IDUser)
      LEFT JOIN Category AS C ON (C.IDCategory = T.IDCategory)
      LEFT JOIN TicketDepartment AS TD ON (TD.IDTicket = T.IDTicket)
      LEFT JOIN Department AS D ON (D.IDDepartment = TD.IDDepartment) ' .
    $StWhere   .
    $StGroupBy .
    $StOrderBy .
    $StLimit;
   
    self::reset(); 
  }
  
  public static function debug($StVariable = NULL, $StMethod = NULL, $BoPrint = TRUE ){
    if (!empty($StVariable)){
      print_r(self::$$StVariable);
    }
        
    if ( !empty($StMethod) && $BoPrint ){
      try{
        eval('print_r(self::' . $StMethod . ');');
      }
      catch (ErrorException $e){
        print_r($e);
      }
    }   
  }
  

  
}

/*SearchHandler::reset();
SearchHandler::setLogged(3);
SearchHandler::debug(NULL, "setSupporter(2)");
SearchHandler::debug(NULL, "setSelectCustomField(array('F1', 'F2'))");
SearchHandler::debug(NULL, "setWhereCustomField(array('LastName'=>'Gomes', 'Occupation'=>'Programmer'))");
SearchHandler::debug(NULL, "setUser('cli%')");
SearchHandler::debug(NULL, "setDepartment(8)");
SearchHandler::debug(NULL, "setCategory(8)");
SearchHandler::debug(NULL, "setDtStartEnd('2009-01-01','2009-03-03')");
SearchHandler::debug(NULL, "setTicket(2)");
SearchHandler::debug(NULL, 'setLimit(30,9)');
SearchHandler::debug(NULL, 'setOrderBy(array("Ticket"=>"DtOpened"))');
SearchHandler::debug(NULL, 'setGroupBy(array("Ticket"=>"IDUser", "Ticket"=>"IDSupporter"))');

print '<pre>';
SearchHandler::debug('ArWhere');
SearchHandler::Search();
print '</pre>';


SearchHandler::reset();*/
/*
SearchHandler::debug('StLimit');*/
//print_R($ArIDent);
//print_R($ArDepartment);

?>