<?php

abstract class WordHandler{
  private static $StText = NULL;
  private static $ArWord = NULL;
  private static $ArWordValidated = NULL;
  private static $ArIDWord = NULL;
  
  
  private static function WordValidate(){
    $ArWord = (array)self::$ArWord;
    $ArWordValidated = array();
    
    if (empty($ArWord)){
      return FALSE;
    }
    
    #
    # Array with undesirable and desirable chars
    # Replace the undesirable chars
    #
    
    $ArCharOld = explode(' ', 'À Á Â Ã Ä Å Æ Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö Ø Ù Ú Û Ü Ý Þ ß à á â ã ä å æ ç è é ê ë ì í î ï ð ñ ò ó ô õ ö ø ù ú û ý ý þ ÿ Ŕ ŕ' );
    $ArCharNew = explode(' ', 'a a a a a a a c e e e e i i i i d n o o o o o o u u u u y b b a a a a a a a c e e e e i i i i d n o o o o o o u u u y y b y R r');
    $ArReplace = array_combine( $ArCharOld, $ArCharNew );

    foreach ( $ArWord as $StWord ){
      $StWord = strtr( self::$StWord, $ArReplace );
      #
      # Valid chars
      #
      $StWord = preg_replace('/[^ [:alnum:]]/', ' ', $StWord );
      $StWord = trim( strtoupper($StWord) );
      $ArWordValidated[] = $StText;
    }
    self::$ArWord = NULL;
    self::$ArWordValidated = (array)$ArWordValidated;
  }
  
  private static function WordExplode(){
    $ArWord = array();
    $ArWordNew = array();
    
    # Removes the white spaces in the words
    $ArWord = preg_split(  "/\s*[-\s]/", strip_tags(self::$StText), NULL,  PREG_SPLIT_NO_EMPTY );
    
    # Removes duplicated values
    $ArWord = array_unique($ArWord);
    
    # Filters some words
    
    $ArFilter = array( 'DE', 'DO', 'DA', 'DOS', 'DAS', 'COM', 'PARA', 'ME', 'MIM', 'COMIGO',
    'EU', 'TU', 'PELO', 'PELA', 'NOS', 'ELE', 'ELA', 'ELAS', 'ISTO', 'ISSO', 'ESTE', 'ESSES',
    'ESSE', 'ESTES', 'AQUELE', 'UM', 'UMA', 'OI', 'OLA', 'OBRIGADO', 'OBRIGADA', 'SIM', 'NAO',
    'TALVEZ', 'AGORA', 'ATENCIOSAMENTE', 'DISPONHA', 'AQUILO', 'AQUELES', 'OUTRO', 'OUTROS',
    'NOVAMENTE', 'ACIMA', 'ABAIXO', 'ATE', 'NUNCA', 'FIZ', 'SEU', 'MEU', 'NOSSO', 'SEUS',
    'MEUS', 'NOSSOS', 'MINHA', 'MINHAS','PREZADO', 'PREZADA', 'CARA', 'CARO', 'DEAR', 'THANKS' );
    
    # Removes numbers and elements with one char
    
    foreach ( (array)$ArWord as $StWord ){
       if ( isset($StWord[2]) && !is_numeric($StWord) ){
          $ArWordNew[] = $StWord;
       }
    }
    self::$ArWord = array_diff( $ArWordNew, $ArFilter );
    return true;
  }
  
  private static function WordSearchID(){
    $ArWord = self::$ArWordValidated;
  }
  
  public static function getIDWords( $StText ){
    self::$StText = $StText;
    self::WordExplode();
    self::WordValidate();
  }
}

?>