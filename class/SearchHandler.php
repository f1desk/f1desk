<?
/**
 * UTF-8
 */
 function dai($Param){
    die(print_R($Param, 1) . '>>>>>>>');
 }

require_once( dirname(__FILE__) . '/../main.php'); //so se for pra teste mesmo !

Class SearchHandler extends DBHandler {
   private $IDTicket;
   private $IDWord; # current word ID
   private $StText;
   private $ArWord;
   private $ArSource;
   private $ArResult;
   private $ItPage;
   public $ArFieldSelect; 
   public $ArWhere; 
   public $ArFrom; 
   public $BoAsc;
   public $ItLimit;
   public $StOrderBy;
   public $StGroupBy;
   public $StSQL;
   public $StSelect;
   public $StWhere;
   public $StManualWhere;
   public $StType;
   public $StFrom;


   public function __construct(){
      parent::__construct();
   }

   public function TextIndex( $IDTicket, $StText ){
      self::$StText = $StText;
      self::$IDTicket = $IDTicket;
      self::WordValidate();
      self::WordExplode();
      $this->WordExists();

      if (  !empty( self::$ArWord )  ){
         $this->WordInsert();
      }
      return true;
   }

   /**
    * Sets the property $StText and calls the methods
    * WordValidade() and WordExplode(), setting, this way,
    * the property $ArWord too.
    *
    * @param  string  $StText
    * @return boolean
    *
    * @author Danilo Gomes <danilo@digirati.com.br>
    *
    **/

   public function setText( $StText = '' ){

      if (empty($StText)){
         return false;
      }

      self::$StText = $StText;
      return true;
   }

   private static function WordExplode(){

      $ArWord = array();
      $ArWordNew = array();

      # Removes the white spaces in the words
      $ArWord = preg_split(  "/\s*[-\s]/", strip_tags(self::$StText)  );

      # Removes duplicated values
      $ArWord = array_unique($ArWord);

      # Filters some words

      $ArFilter = array( 'DE', 'DO', 'DA', 'DOS', 'DAS', 'COM', 'PARA', 'ME', 'MIM', 'COMIGO',
      'EU', 'TU', 'PELO', 'PELA', 'NOS', 'ELE', 'ELA', 'ELAS', 'ISTO', 'ISSO', 'ESTE', 'ESSES',
      'ESSE', 'ESTES', 'AQUELE', 'UM', 'UMA', 'OI', 'OLA', 'OBRIGADO', 'OBRIGADA', 'SIM', 'NAO',
      'TALVEZ', 'AGORA', 'ATENCIOSAMENTE', 'DISPONHA', 'AQUILO', 'AQUELES', 'OUTRO', 'OUTROS',
      'NOVAMENTE', 'ACIMA', 'ABAIXO', 'ATE', 'NUNCA', 'FIZ', 'SEU', 'MEU', 'NOSSO', 'SEUS',
      'MEUS', 'NOSSOS', 'MINHA', 'MINHAS','PREZADO', 'PREZADA', 'CARA', 'CARO', 'DEAR', 'THANKS' );

      # Retira os elementos com apenas um caractere

      foreach ( (array)$ArWord as $StWord ){
         if ( strlen($StWord) > 1 && ! is_numeric($StWord) )
            $ArWordNew[] = $StWord;
      }
      self::$ArWord = array_diff( $ArWordNew, $ArFilter );
      return true;
   }


   #
   # Make the word's validate
   #

   private static function WordValidate(){

      #
      # Array with undesirable and desirable chars
      # Replace the undesirable chars
      #

      $ArCharOld = explode(' ', 'À Á Â Ã Ä Å Æ Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö Ø Ù Ú Û Ü Ý Þ ß à á â ã ä å æ ç è é ê ë ì í î ï ð ñ ò ó ô õ ö ø ù ú û ý ý þ ÿ Ŕ ŕ' );
      $ArCharNew = explode(' ', 'a a a a a a a c e e e e i i i i d n o o o o o o u u u u y b b a a a a a a a c e e e e i i i i d n o o o o o o u u u y y b y R r');

      $ArReplace = array_combine( $ArCharOld, $ArCharNew );
      $StText = strtr( self::$StText, $ArReplace );

      #
      # Just valid chars
      #

      $StText = preg_replace('/[^ [:alnum:]]/', ' ', $StText );
      $StText = trim( strtoupper($StText) );

      self::$StText = $StText;

      return true;
   }
   
   private function prepareWhere(){
      
      if (!empty($this->StWhere)){
         return true;
      }
      
      if ( empty($this->ArWhere) || !is_array($this->ArWhere) ){
         throw new 
         ErrorHandler( 'ArWhere empty at call search in prepareWhere.' );
      }
      
      #
      # Fields and values to compare in WHERE clause
      #
      $ArField = array_keys( $this->ArWhere );
      $ArValue = array_values( $this->ArWhere );
     
      #
      # The number of elements should be equal in both arrays
      #
      
      if ( count($ArField) != count($ArValue) ){
         throw new ErrorHandler('Bad arguments to search call');
      }
      $ItCount = count($ArField);
      
      #
      # Mounts the WHERE clause
      #

      for ( $i=0; $i<$ItCount; $i++ ){
         #
         # Numeric values are better compared without quotes in MySQL
         #
         if ( strpos( $ArField[$i], 'ID' ) !== FALSE ){
            $ArWhere[] = "$ArField[$i] = $ArValue[$i]";
            continue;
         }
         else{
            $ArWhere[] = "$ArField[$i] = '$ArValue[$i]'";
            continue;
         }       
      }
      $this->StWhere = implode( ' AND ' , $ArWhere );
   }

   private function prepareSelect( $StType = 'Ticket'){      
      $ArFieldSelect = $this->ArFieldSelect;
      foreach ((array)$ArFieldSelect as $Key=>$ArTable){
         #
         # All FROM tables
         #
         $ArFrom[] = $Key;
         foreach ((array)$ArTable as $ArField){
            if ( strtolower($Key) != 'supporter'){
         	  $ArSelect[] = "$Key.$ArField";
            }
            else{
              $ArSelect[] = "User.$ArField";
            }
         }
      }
      $this->prepareFrom( $ArFrom, $StType );
      $this->StSelect = implode( ' , ', (array)$ArSelect );
      unset($ArFieldSelect, $ArFrom );
   }
   
   private function prepareFrom( $ArFrom, $StType = 'Ticket' ){
      if (empty($ArFrom)){
         throw  new
         ErrorHandler('ArFrom cannot be initializated in SearchHandler - prepareFrom');
      }
            
      if ( $StType == 'Ticket' ){
         $StFrom = ' Ticket ';
         
         if (in_array('User', $ArFrom)){
            $StFrom .= ' LEFT JOIN User ON ( Ticket.IDUser = User.IDUser ) ';
         }
         if (in_array('Rate', $ArFrom)){
            $StFrom .= ' LEFT JOIN Rate ON ( Ticket.IDRate = Rate.IDRate ) ';
         }
         if (in_array('Category', $ArFrom)){
            $StFrom .= ' LEFT JOIN Category ON ( Ticket.IDCategory = Category.IDCategory ) ';
         }
         if (in_array('Priority', $ArFrom)){
            $StFrom .= ' LEFT JOIN Priority ON ( Ticket.IDPriority = Priority.IDPriority ) ';
         }
         if (in_array('Supporter', $ArFrom)){
            $StFrom .=' LEFT JOIN  Supporter ON ( Ticket.IDSupporter = Supporter.IDSupporter) ';
         }
      }
      elseif( $StType == 'Supporter' ){
         $StFrom = 
         ' Supporter LEFT JOIN User ON (User.IDUser = Supporter.IDSupporter) ';
         
         if (in_array('Unit', $ArFrom)){
            $StFrom .= ' LEFT JOIN Unit ON ( Supporter.IDSupporter = Unit.IDUnit ) ';
         }
         if (in_array('Department', $ArFrom) || in_array('SubDepartment', $ArFrom)){
            $StFrom .= 
            ' LEFT JOIN DepartmentSupporter ON
                  (Supporter.IDSupporter = DepartmentSupporter.IDSupporter)
              LEFT JOIN Department ON 
                  (DepartmentSupporter.IDDepartment = Department.IDDepartment ) ';

            if ( in_array('SubDepartment', $ArFrom) ){
              $StFrom .=
              ' LEFT JOIN SubDepartment ON 
                  (Department.IDDepartment = SubDepartment.IDDepartment ) ';
            }
         }
      }  
         #
         # Set the FROM
         #
         $this->StFrom = $StFrom;
   }
   
   
   private function getLimit(){
      $StLimit = '';
      $ItLimit = $this->ItLimit;
      $ItPage = $this->ItPage;
      
      if ( !empty($ItLimit) && is_numeric($ItLimit) ){
         #
         # Paginacao
         # 
         if ( !empty($ItPage) && is_numeric($ItPage) && ($ItPage > 1) ){
            $ItStart = $ItLimit * ($ItPage - 1);
            $StLimit = ' LIMIT ' . $ItStart . ',' . $ItLimit;
         }
         else{
            $StLimit = ' LIMIT ' . $ItLimit;     
         }
      } 
      return $StLimit;
   }
   
   private function getOrderBy(){
      $StOrderBy = '';
      if ( !empty($this->StOrderBy) ){
         if (empty($BoAsc)){
            $Sort = ' ASC ';            
         }
         else{
            $Sort = ' DESC ';
         }
         $StOrderBy = ' ORDER BY ' . $this->StOrderBy . " $Sort ";     
      }
      return $StOrderBy;
   }
   
   private function getGroupBy(){
      $StGroupBy = '';
      if ( !empty($this->StGroupBy) ){
         $StGroupBy = ' GROUP BY ' . $this->StGroupBy;     
      }
      return $StGroupBy;
   }
   
   public function WordInsert() {

      $ArParam = array();

      foreach (  (array) self::$ArWord as $StWord   ){
         $ArParam[] = array( NULL, $StWord, 0 );
      }

      #
      # Insert words in database
      #

      $ArField = array( 'IDWord', 'StWord', 'ItCount' );
      $this->insertIntoTable( DBPREFIX . 'Word', $ArField, $ArParam );

   }

   private function WordExists() {

      $ArData = array();
      $ArTrash = array();
      $ArID = array();

      $StWords = "'" . implode( "', '" , self::$ArWord ) . "'";

      $SQL = '
      SELECT
         StWord
      FROM '
      .  DBPREFIX . 'Word
      WHERE
         StWord
      IN
         ( '. $StWords .' )';

      $this->execSQL( $SQL );
      $ArRemove = $this->getResult( 'row' );

      if (  is_array($ArRemove)  ){
         foreach ( $ArRemove as $Remove ){
            $ArTrash[] = $Remove[0];
         }
         #
         # Removes the existents words
         #
         self::$ArWord = array_diff( self::$ArWord, $ArTrash );
      }

      return true;
   }

   public function SearchTicket( ){
           
      #
      # Select all required fields from Ticket table
      # and sets $this->StSelect variable
      #
      

      $this->prepareSelect( 'Ticket' );     
      
      $this->prepareWhere();
      
      #
      # Validation
      #
      
      $StSQL = '
      SELECT '
      .  $this->StSelect . ' 
      FROM ' .
         $this->StFrom . '
      WHERE '
      .  $this->StWhere
      .  $this->getGroupBy()
      .  $this->getOrderBy()
      .  $this->getLimit();
throw new ErrorHandler($StSQL);

      $this->execSQL( $StSQL );
      $Result = $this->getResult('string');
      
      $ArReturn = array();
      foreach ( (array)$Result as $ArTicket ){
         $ArReturn[] = $ArTicket;
      }
         
      return $ArReturn;
      
   }
   
   public function setParam( $ArFieldSelect, $ArWhere = '', $StManualWhere, $ArWord = array(), $StGroupBy = '', $StOrderBy = '', $ItLimit = 0, $ItPage = 0  ){
      $this->ArFieldSelect = $ArFieldSelect;
      $this->ArWhere = $ArWhere;
      $this->StGroupBy = $StGroupBy;
      $this->StOrderBy = $StOrderBy;
      $this->ItLimit = $ItLimit;
      $this->StManualWhere = $StManualWhere;
      $this->ArWord = $ArWord;
      $this->ItPage = $ItPage;
      
      if (!empty($StManualWhere)){
         $this->StWhere = $StManualWhere;
      }
      
   }
   
   public function SearchTicketByID( $IDTicket ){
      
      if (empty($IDTicket)){
         throw new ErrorHandler('Invalid IDTicket to SelectTicketByID.');
      }
      $this->prepareSelect( 'Ticket' );
      
      $StSQL =
      '  SELECT '
      .     $this->StSelect
      .' FROM '
      .     $this->StFrom
      .' WHERE 
            Ticket.IDTicket = ' . $IDTicket; 
      
      $this->execSQL( $StSQL );
      $Result = $this->getResult('string');
      
      if (is_array($Result) && !empty($Result)){
        return $Result[0];
      }
      return array();
   }
   
   public function SearchSupporterByID( $IDSupporter ){
      if ( empty($IDSupporter) || !is_numeric($IDSupporter) ){
         throw new ErrorHandler( 'IDUser cannot be initializated in SearchUserByID.' );
      }
      
      $this->prepareSelect( 'Supporter' );
      
      $StSQL =
      '  SELECT '
      .     $this->StSelect
      .' FROM '
      .     $this->StFrom
      .' WHERE 
            Supporter.IDSupporter = ' . $IDSupporter; 

      $this->execSQL( $StSQL );
      $Result = $this->getResult('string');
      
      if (is_array($Result) && !empty($Result)){
        return $Result[0];
      }
      
      return array();
   }
   
   public function SearchSupporter( ){
      
      $this->prepareSelect( 'Supporter' );
      $this->prepareWhere();
      
      $StSQL =
      '  SELECT '
      .     $this->StSelect
      .' FROM '
      .     $this->StFrom
      .' WHERE '
      .  $this->StWhere
      .  $this->getGroupBy()
      .  $this->getOrderBy()
      .  $this->getLimit();
//throw new ErrorHandler($StSQL);
      dai($StSQL);
      $this->execSQL( $StSQL );
      $Result = $this->getResult('string');
      
      $ArReturn = array();
      foreach ( (array)$Result as $ArTicket ){
         $ArReturn[] = $ArTicket;
      }
      return $ArReturn;
   }
   
   private function SearchWordIDs( ){
      # Continuar daqui
      $StText = implode( ',' , $this->ArWord);
      
      #
      # Search for IDWords in Word Table
      #
      
      $StSQL = 
      'SELECT 
         IDWord 
       FROM 
         Word 
       WHERE 
         StWord
       IN (' . $StText . ')';
      
       $this->execSQL( $StSQL );
       $Result = $this->getResult('string');
       
       $ArWord = array();
       foreach ( (array)$Result as $ArResult ){
          $ArWord[] = $ArResult;
       }
      
       return $ArWord;
   }
   
   private function SearchTicketWithWord( $IDWord ){
      
     // $ArIDWord = $this->ArIDWord;
      $StSource = implode( ',', $this->ArSource );
      
         $StSQL = 
         'SELECT
            IDTicket
          FROM
            WordTicket
          WHERE
            IDWord = ' . $IDWord;
         
         $this->execSQL( $StSQL );
         $ArResult = $this->getResult('string');      
         
         $this->ArSource = $ArResult;
   }
   
   public function SearchByWord(){
      $this->$ArSource = $this->SearchTicket();
      $ArIDWord = $this->SearchWordIDs();
      
      foreach ((array)$ArIDWord as $IDWord){
         $this->SearchTicketWithWord($IDWord);
      }
      
      $ArSource = $this->ArSource;
      
      foreach ( (array)$ArSource as $IDTicket )
      $ArResult[] = $this->SearchTicketByID($IDTicket);
      
   }
}


$TicketSearch = new SearchHandler();

#**************************** TESTE CHAMADO
$Arwhere = array(  'Category.IDCategory'=>3 );
$ArSelect = array( 
               'Ticket'=>array('StTitle'),
            //'Department'=>array('IDDepartment'),
            'Category'=>array( ),
       //     'SubDepartment'=>array('IDDeparment'),
      //      'Department'=>array() 
      );

#*****************************

########## TESTE Supporter
/*
$Arwhere = array('Supporter.IDSupporter' => 5,  'User.StName'=> 'opa' );



$ArSelect = array( 
   'Supporter' => array('StName', 'StEmail')
);      */
###################################*/
//$StManualWhere = 'teste';
//$TicketSearch->prepareWhere($Arwhere);// = $Arwhere;

//print_R($TicketSearch->StWhere);
//$TicketSearch->ArFieldSelect = $ArFieldSelect;
$ArWord = '';
$StManualWhere ='';
$TicketSearch->setParam($ArSelect, $Arwhere, $StManualWhere, $ArWord, '', '' );

//$TicketSearch->SearchTicketByID(112);
$TicketSearch->SearchTicket();
//$TicketSearch->SearchSupporter();

//$TicketSearch->SearchSupporterByID('3');



/*$Texto = 'Since 1990, nova texto and Gab5riela çãçá <><><><[[[[]]] @!#$$$ ++_+_+=------==== ¬¬ ﾬ 654141 Martinez bring you the talent and the tools for your graphic design needs. texto is full service graphic design and technical communication group that combines high-end design talent with an extensive background in technical documentation.';

$TicketSearch = new TicketSearch();

$TicketSearch->TextIndex( '7777777', $Texto );*/
?>