<?
/**
 * Classe DBHandler
 *
 * @author Matheus Ashton <matheus@fatorweb.com>
 *
 */

class DBHandler {
   private $ObjConnection;
   public  $RsResult;

   /**
    * Metodo construtor da classe
    *
    * @return resource RsObjConnection
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function __construct($isUser = false) {
      if (! $this->ObjConnection) {
         $this->ObjConnection = mysqli_init();

         if ($isUser) {
           $Connection = $this->ObjConnection->real_connect(USERDBHOST,USERDBUSER,USERDBPASS,USERDBNAME);
         } else {
           $Connection = $this->ObjConnection->real_connect(DBHOST,DBUSER,DBPASS,DBNAME);
         }

         if(! $Connection ) {
            throw new ErrorHandler(mysqli_connect_error(),mysqli_connect_errno());
            return false;
         } else {
            $this->ObjConnection->autocommit(FALSE);
            return $this->ObjConnection;
         }
      }
   }

   /**
    * Armazena as transacoes a serem realizadas
    *
    * @param str StQuery
    *
    * @return VOID
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function setQuery($StQuery) {
      $setQuery = $this->ObjConnection->query($StQuery);
      if( $setQuery === false ) {
         throw new ErrorHandler(EXC_DB_QUERY . ' Detalhes:' . $this->ObjConnection->error);
      }
   }

   /**
    * Executa uma consulta SQL que retornara um resource
    *
    * @param str StQuery
    *
    * @return VOID
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function execSQL($StQuery) {
      $this->RsResult = $this->ObjConnection->query($StQuery);
      if( $this->RsResult === false ) {
         throw new ErrorHandler(EXC_DB_EXEC . ' Detalhes:' . $this->ObjConnection->error);
      }
   }

   /**
    * Efetua as transacoes armazenadas
    *
    * @return VOID
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function commit() {
      $commit = $this->ObjConnection->commit();
      if( $commit === false ) {
         throw new ErrorHandler(EXC_DB_EXEC);
      }
   }


   /**
    * Obtem o numero de linhas de um determinado resultado
    *
    * @return int ItNumRows
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function getNumRows() {
      $ItNumRows = $this->RsResult->num_rows;
      if($ItNumRows === false) {
         throw new ErrorHandler(EXC_DB_NUMROWS);
      } else {
         return $ItNumRows;
      }
   }


   /**
    * Obtem o numero de linhas afetadas por uma transacao SQL
    *
    * @return int ItAffected
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function getAffectedRows() {
      $ItAffected = $this->ObjConnection->affected_rows;
      if($ItAffected === false) {
         throw new ErrorHandler(EXC_DB_AFFECTEDROWS);
      } else {
         return $ItAffected + 1;
      }
   }

   /**
    * Obtem o ultimo ID incluido
    *
    * @return int ID
    *
    * @author Dimitri Lameri <contato@dimitri.x-br.com>
    */
   public function getID() {
      $this->execSQL('SELECT LAST_INSERT_ID() as ID;');
      $ID = $this->getResult('string');
      if( $ID === false ) {
         throw new ErrorHandler(EXC_DB_LASTID);
      } else {
         return $ID[0]['ID'];
      }
   }

   /**
    * Retorna o array de indices mistos com o resultado de uma consulta efetuada
    *
    * @return Array ArFetchArray
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function getResult($StTipo = "both") {
      $ArFetchArray = array();

      if ($StTipo == "both"){
         while ($ArRow = $this->RsResult->fetch_array(MYSQLI_BOTH)) {
            $ArFetchArray[] = $ArRow;
         }
      }
      elseif ($StTipo == "string") {
         while ($ArRow = $this->RsResult->fetch_array(MYSQLI_ASSOC)) {
            $ArFetchArray[] = $ArRow;
         }
      }
      else {
         while ($ArRow = $this->RsResult->fetch_array(MYSQLI_NUM)) {
            $ArFetchArray[] = $ArRow;
         }
      }

      if($ArFetchArray === false ) {
         throw new ErrorHandler(EXC_DB_GETRESULT);
      }
      else
         return $ArFetchArray;
   }

   /**
    * Insert data on a specified table
    *
    * @param  str     $StTable    Name of the table that will receive the data
    * @param  array   $ArData     Array keys being the name of the fields and values the data to be inserted.
    * @return boolean true/false
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   function insertIntoTable($StTable,$ArFields, $ArData) {
     $StNewData = '';
     if (! is_array($ArData) || empty($StTable)) {
       return false;
     }

     $StFields = implode(',',$ArFields);

     if (is_array($ArData[0])) {
       foreach ($ArData as &$StValues) {

         if (count($StValues) != count($ArFields)) {
     	     throw new ErrorHandler(EXC_GLOBAL_EXPPARAM);
         }

         foreach ($StValues as &$Valores) {
           $Valores = "'".addslashes($Valores)."'";
         }

     	   $StValues = implode(",",$StValues);
     	 }
     } else {
       foreach ($ArData as &$Data) {
         $Data = "'".addslashes($Data)."'";
       }
       $ArData = array(implode(',',$ArData));
     }

     $StSQL = "
INSERT INTO
  $StTable($StFields)
VALUES
  ($ArData[0])";

     $Limit = count($ArData);

     if ($Limit >= 2) {
       for ($i=1;$i<$Limit;$i++) {
         $StSQL .= ", ({$ArData[$i]})";
       }
     }
     $this->setQuery($StSQL);
     $this->commit();
     $ItAffected = $this->getAffectedRows();

     if($ItAffected <= -1) {
       return false;
     }

     return $ItAffected;
   }

   /**
    * Delete a row from a specified table with a specified condition
    *
    * @param str $StTable       Name of the table
    * @param str $StConditions  Conditions to the exclusion
    * @return int/bool  Number of affected rows, false if fails
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   function deleteFromTable($StTable,$StConditions, $ItLimit = 0) {
     $StConditions = trim($StConditions);
     if (empty($StConditions) || empty($StTable)) {
       throw new ErrorHandler(EXC_GLOBAL_EXPPARAM);
     }

     $StSQL = "DELETE FROM $StTable WHERE $StConditions";

     if ($ItLimit) {
       $StSQL .= " Limit $ItLimit";
     }

     $this->setQuery($StSQL);
     $this->commit();

     $ItAffected = $this->getAffectedRows();
     if ($ItAffected <= -1) {
       return false;
     }

     return $ItAffected;
   }

   function updateTable($StTable,$ArData,$StConditions, $ItLimit = 0) {
     $StConditions = trim($StConditions);
     if (empty($StConditions) || empty($StTable)) {
       throw new ErrorHandler(EXC_GLOBAL_EXPPARAM);
     }

     $ArFields = array_keys($ArData);
     $FirstKey = array_shift($ArFields);
     $FirstValue = array_shift($ArData);

     $StSQL = "UPDATE $StTable SET
              $FirstKey = '$FirstValue'";
     foreach ($ArData as $Field => $Value) {
       $StSQL .= ", $Field = '$Value'";
     }
     $StSQL .= " WHERE $StConditions";

     if ($ItLimit) {
       $StSQL .= " Limit $ItLimit";
     }

     $this->setQuery($StSQL);
     $this->commit();

     $ItAffected = $this->getAffectedRows();
     if ($ItAffected <= -1) {
     	 return false;
     }

     return $ItAffected;
   }

   /**
    * Fecha a conexao com o banco
    *
    * @return VOID
    *
    * @author Matheus Ashton <matheus@digirati.com.br>
    */
   public function close() {
      if(! $this->ObjConnection->close()) {
         throw new ErrorHandler(EXC_DB_DISCONNECT);
      }
   }
}
?>