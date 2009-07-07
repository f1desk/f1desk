<?php

require_once('../main.php');
require_once('../lang/pt_BR/lang.SearchHandler.php');

abstract class SearchHandler{
  private static $DBHandler;
  private static $IDTicket;
  private static $IDDepartment;
  private static $IDSupporter;
  /*private static $DtStart;
  private static $DtEnd;*/
  private static $ItPage;
  private static $ItLimit;
  private static $ArStWord;
  private static $ArIDWord;
  private static $ArData;
  private static $ArIDDepartment;  
  private static $ArCustomField;
  private static $ArWhere;
  private static $ArOrderBy;
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
  
  private static function IDValidate( $ItID, $StError = NULL ){
    # se o argumento foi informado, mas nao e' numerico
    if ( !empty($ItID) && !is_numeric($ItID) ){
      if ( empty($StError)){
        $StError = EXC_BAD_ARGUMENT;
      }
      throw new errorHandler( sprintf($StError, $ItID) );
    }    
    return (int)$ItID;
  }
     
  private static function getDBinstance(){
		if ( ! self::$DBHandler instanceof DBHandler ) {
			self::$DBHandler = new DBHandler();
		}
	}
	
	private static function makeSelectCustomField(){
	  $ArCustomField = self::$ArCustomField;
	  self::$ArCustomField = NULL;
	  
	  if ( empty($ArCustomField) || !is_array($ArCustomField)){
	    return '';
	  }
	  #
	  ## User Table Alias 'U.' is not required in call time
	  #
	  foreach ($ArCustomField as &$StField ){
	    $StField = 'U.' . addslashes($StField);
	  }
	  #
	  ## $StSelectCustomField starts select sentence, so, we put ',' in the end
	  #
	  $StSelectCustomField = implode(',', $ArCustomField) . ',';
	  return $StSelectCustomField;
	}
	
	private static function makeWhere(){
	  $ArWhere = self::$ArWhere;
	  self::$ArWhere = NULL;
	  
	  if (empty($ArWhere) || !is_array($ArWhere)){
	    return '';
	  }
	  $StWhere = implode( ' AND ', $ArWhere );
	  return $StWhere;
	}
	
	private static function makeGroupBy(){
	  
	}
	
	private static function makeOrderBy(){
	  
	}
	
	private static function makeLimit(){
	  
	}
	
	private static function ArWhereAdd( $StElement ){
	  if (!empty($StElement)){
   	  $ArWhere = self::$ArWhere;
	    $ArWhere[] = $StElement;
	    self::$ArWhere = $ArWhere;
	  }
	}

	public static function setDepartment( $IDDepartment = NULL ){
	  $IDDepartment = self::IDValidate($IDDepartment,EXC_INVALID_IDDEPARTMENT);
	  if (!empty($IDDepartment)){
  	  $StWhere = " T.IDDeparment IN ( $IDDepartment ) ";
  	  self::ArWhereAdd($StWhere);
	  } 
	}
	
	public static function setSupporter( $IDSupporter = NULL ){
	  self::$IDSupporter = self::IDValidate($IDSupporter, EXC_INVALID_IDSUPPORTER);
	}
	
	public static function setTicket( $IDTicket = NULL ){
	  self::$IDTicket = self::IDValidate($IDTicket, EXC_INVALID_IDTICKET);
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
          $Dt = $DtStart;
          $DtStart = $DtEnd;
          $DtEnd = $Dt;
          unset($Dt);
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
      $ArWhere = (array)self::$ArWhere;
      array_unshift($ArWhere, $StWhere);
      self::$ArWhere = $ArWhere;      
	}
	
	public static function setGroupBy( $StGroupBy = NULL ){
	  self::$StGroupBy = $StGroupBy;
	}
		
	public static function setOrderBy( $StOrderBy = NULL ){
	  self::$StOrderBy = $StOrderBy;
	}

	public static function setLimit( $ItLimit = NULL, $ItPage = NULL ){
    self::$ItLimit = 50;
	  if ( $ItLimit = self::IDValidate($ItLimit, EXC_INVALID_LIMIT) ){
	    self::$ItLimit = $ItLimit;
	  }	  
	  
	  self::$ItPage = 1;
	  if ( $ItPage = self::IDValidate($ItPage, EXC_INVALID_PAGE) ){
	    self::$ItPage = $ItPage;
	  }	  	  
	}	
	
	public static function setArWord( $ArWord = array() ){
	  self::$ArStWord = (array)$ArWord;
	}
	
	public static function setArCustomField( $ArCustomField = array() ){
	  self::$ArCustomField = (array)$ArCustomField;
	}
   
  public static function setData( $IDDepartment = NULL , $IDSupporter = NULL, $DtStart = NULL, $DtEnd = NULL, $ArWord = NULL, $ItPage = 1, $StOrderBy = NULL, $StGroupBy = NULL, $ItLimit = NULL ){    
    self::getDBinstance();
    self::setDtStartEnd($DtStart, $DtEnd);
    self::setDepartment($IDDepartment);
    self::setSupporter($IDSupporter);
    self::setArWord($ArWord);
    self::setLimit($ItLimit, $ItPage);
  }
  
  public static function Search(){
    $StSelectCustomField = self::makeSelectCustomField();
    $StWhere = self::makeWhere();
    $StGroupBy = self::makeGroupBy();
    $StOrderBy = self::makeOrderBy();
    $StLimit = self::makeLimit();
    
    $SQL = 
    'SELECT ' . $StSelectCustomField .
    ' T.IDTicket, T.StTitle, T.DtOpened, T.IDSupporter U.IDUser, D.StDepartment, C.StCategory
     FROM
      Ticket AS T
      LEFT JOIN User AS U ON (U.IDUser = T.IDUser)
      LEFT JOIN Category AS C ON (C.IDCategory = T.IDCategory)
      LEFT JOIN TicketDeparment AS TD ON (TD.IDTicket = T.IDTicket)
      LEFT JOIN Department AS D ON (D.IDDeparment = TD.IDDeparment) ' .
    $StWhere   .
    $StGroupBy .
    $StOrderBy .
    $StLimit;
    
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
/*

SearchHandler::debug(NULL, 'setDepartment()');
SearchHandler::debug('ArWhere');*/

$ArDepartment = F1DeskUtils::getDepartments(2);
$ArDepartment = array_keys($ArDepartment);

$ArIDDepartment = array();

foreach ($ArDepartment as $IDDepartment=>$StDepartment){
  if ( is_numeric($IDDepartment) ){
    $ArIDDepartment[] = $IDDepartment;
  }
}

print_R($ArIDDepartment);

?>