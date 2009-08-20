<?php

require_once('../main.php');
require_once(INCLUDESDIR . 'settings.php');

abstract class WordHandler{
  private static $StText = NULL;
  private static $ArWord = NULL;
  private static $ArWordDB = NULL;
  private static $ArNewWord = NULL;
  private static $ArIDWord = NULL;  
  private static $ItWordMinSize = NULL;
  
  private static function reset(){
    self::$StText = NULL;
    self::$ArWord = NULL;
    self::$ArWordDB = NULL;
    self::$ArNewWord = NULL;
    self::$ArIDWord = NULL;
  }
  
  private static function WordValidate(){
    $ArWord = (array)self::$ArWord;
    $ArWordValidated = array();
    
    if (empty($ArWord)){
      return FALSE;
    }
    
    #
    # Array with undesirable and desirable chars
    # The undesirable chars are replaced
    #
    
    $ArCharOld = explode(' ', 'À Á Â Ã Ä Å Æ Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö Ø Ù Ú Û Ü Ý Þ ß à á â ã ä å æ ç è é ê ë ì í î ï ð ñ ò ó ô õ ö ø ù ú û ý ý þ ÿ Ŕ ŕ' );
    $ArCharNew = explode(' ', 'a a a a a a a c e e e e i i i i d n o o o o o o u u u u y b b a a a a a a a c e e e e i i i i d n o o o o o o u u u y y b y R r');
    $ArReplace = array_combine( $ArCharOld, $ArCharNew );
    
    unset($ArCharOld, $ArCharNew);
    
    #
    # Word's minimum size to save in database
    #
    
    if (empty(self::$ItWordMinSize)){
        self::$ItWordMinSize = getOption('word_min_size');
    }
    
    foreach ( (array)$ArWord as $StWord ){
      $StWord = strtr( $StWord, $ArReplace );
      
      #
      # Valid chars
      #
      
      $StWord = preg_replace('/[^ [:alnum:]]/', ' ', $StWord );
      $StWord = str_replace(' ','', $StWord);
      
      #
      # Only words bigger than "ItWordMinSize" (see "option.xml")
      #
      
      if ( isset($StWord[self::$ItWordMinSize]) && !is_numeric($StWord) ){
        $ArWordValidated[] = addslashes( strtoupper($StWord) );
      }
    }
    self::$ArWord = (array)$ArWordValidated;
  }
  
  private static function WordExplode(){
    
    if (empty(self::$StText)){
      self::$ArWord = array();
      return FALSE;
    }
    
    $ArWord = array();
    $ArWordNew = array();
    
    #
    # Removes the white spaces in the words
    #
    
    $ArWord = preg_split(  "/\s*[-\s]/", strip_tags(self::$StText), NULL,  PREG_SPLIT_NO_EMPTY );
    
    #
    # Removes duplicated values
    #
    
    $ArWord = array_unique($ArWord);
    
    #
    # Filters some words
    #
    
    $ArFilter = array( 'DE', 'DO', 'DA', 'DOS', 'DAS', 'COM', 'PARA', 'ME', 'MIM', 'COMIGO',
    'EU', 'TU', 'PELO', 'PELA', 'NOS', 'ELE', 'ELA', 'ELAS', 'ISTO', 'ISSO', 'ESTE', 'ESSES',
    'ESSE', 'ESTES', 'AQUELE', 'UM', 'UMA', 'OI', 'OLA', 'OBRIGADO', 'OBRIGADA', 'SIM', 'NAO',
    'TALVEZ', 'AGORA', 'ATENCIOSAMENTE', 'DISPONHA', 'AQUILO', 'AQUELES', 'OUTRO', 'OUTROS',
    'NOVAMENTE', 'ACIMA', 'ABAIXO', 'ATE', 'NUNCA', 'FIZ', 'SEU', 'MEU', 'NOSSO', 'SEUS',
    'MEUS', 'NOSSOS', 'MINHA', 'MINHAS','PREZADO', 'PREZADA', 'CARA', 'CARO', 'DEAR', 'THANKS' );
    
    self::$ArWord = array_diff( (array)$ArWord, (array)$ArFilter );
    return TRUE;
  }
  
  private static function selectIDWordFromObject(){
    $ArWord = self::$ArWord;
    if (empty($ArWord) || !is_array($ArWord)){
      return array();
    }    
    $ArWordDB = array();
    $ArIDWord = array();
        
    $StIN = "'" . implode("' , '", $ArWord ) . "'";
    $StSQL = 
    "SELECT IDWord, StWord
       FROM Word 
     WHERE 
       StWord IN ($StIN)
     LIMIT 1000;";
    
    SearchHandler::getDBinstance();
  	SearchHandler::$DBHandler->execSQL($StSQL);
    $ArResult = SearchHandler::$DBHandler->getResult("string");
    
    foreach ((array)$ArResult as $Row){
      $ArIDWord[] = $Row['IDWord'];
      $ArWordDB[] = $Row['StWord'];
    }
    self::$ArNewWord = array_diff(self::$ArWord, $ArWordDB);
    self::$ArWordDB = $ArWordDB;
    self::$ArIDWord = $ArIDWord;    
  }
  
  public static function getIDWords( $MxWord, $BoValidate = TRUE ){
    
    #
    # If needs validate, we've understand that the default argument has given
    # Then, we expected a simple text (string). 
    #
    
    if ( !empty($BoValidate) ){
      self::$StText = $MxWord;
      self::WordExplode();
      self::WordValidate();
      $ArWord = self::$ArWord;
      self::reset();
      unset($MxWord);
    }
    else{      
      
      #
      # Otherwise, we expect an array. Probably, in this case, the method 
      # has been called from own object
      #      
      
      $ArWord = (array)$MxWord;
      unset($MxWord);
    }
 
    if (empty($ArWord) || !is_array($ArWord)){
      return array();
    }
    
    $ArWordDB = array();
    $ArIDWord = array();
        
    $StIN = "'" . implode("' , '", $ArWord ) . "'";
    
    $StSQL = 
    "SELECT IDWord
       FROM Word 
     WHERE 
       StWord IN ($StIN)
     ORDER BY 
       ItCount DESC
     LIMIT 1000;";
    
    SearchHandler::getDBinstance();
  	SearchHandler::$DBHandler->execSQL($StSQL);
    $ArResult = SearchHandler::$DBHandler->getResult("string");
    
    foreach ((array)$ArResult as $Row){
      $ArIDWord[] = $Row['IDWord'];
    }
    self::$ArIDWord = $ArIDWord;
    return $ArIDWord;
  }
  
  public static function setWords($ArWord = array()){
    self::$ArWord = $ArWord;
    if (empty($ArWord) || !is_array($ArWord)){
      self::$ArWord = array();
    }
  }
  
  public static function setText($StText){
    self::$StText = '';
    if ( !empty($StText) && !is_string($StText)){
      self::$StText = $StText;
    }
  }
  
  public static function insertWords( $StText, $IDTicket = NULL ){
    $StValues = '';
    $StSQL = '';
    
    if (!empty($StText) || !is_string($StText)){
      self::$StText = $StText;
    }
    
    self::WordExplode();
    self::WordValidate();
    self::selectIDWordFromObject();
    $ArNewWord = self::$ArNewWord;
    $ArWordDB  = self::$ArWordDB;
    $ArIDWord  = self::$ArIDWord;

    if (empty(self::$ArWord) || !is_array(self::$ArWord)){
      return FALSE;
    }    
    
    if (!empty($ArNewWord) && is_array($ArNewWord)){
      
      #
      # First Word
      #
      
      $StFirstWord = array_shift($ArNewWord);
      $StValues = "('', '$StFirstWord', 1)"; 
      
      #
      # IDWord - autoincrement, StWord AND 1 to count of use
      #
      
      foreach ((array)$ArNewWord as $StWord ){
        $StValues .= ", ('', '$StWord', 1 )"; 
      }
      
      #
      # If the word exists, we'll to increment her count
      #
           
      $StSQL = "INSERT IGNORE INTO Word VALUES $StValues;";
      SearchHandler::$DBHandler->setQuery($StSQL);
    }  

    if ( !empty($ArIDWord) && is_array($ArIDWord)){
      $StIN  =  implode(",", $ArIDWord);
      $StSQL = "UPDATE Word SET ItCount = ItCount+1 WHERE IDWord IN ($StIN);";
      SearchHandler::$DBHandler->setQuery($StSQL);
    }

    #
    # Get all IDWords, including the new words this time
    #
    
    $ArIDWord = self::getIDWords(self::$ArWord, FALSE);
    
    if ( !empty($IDTicket) && is_numeric($IDTicket)){     
      $ItFirstIDWord = array_shift($ArIDWord);
      $StValues = "( $ItFirstIDWord, $IDTicket )"; 
            
      foreach ((array)$ArIDWord as $IDWord ){
        $StValues .= ", ( $IDWord, $IDTicket )"; 
      }
              
      $StSQL = "REPLACE INTO WordTicket VALUES $StValues;";
      SearchHandler::$DBHandler->setQuery($StSQL);
    }
    SearchHandler::$DBHandler->commit();
    unset($StFirstWord, $StValues, $StSQL);    
  }  
}
//WordHandler::insertWords('danilo mario john dimitri',4);
//print_R(WordHandler::getIDWords('feliz prezado Helpdesk'));


?>