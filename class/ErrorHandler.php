<?php

/**
 * UTF-8
 */


#
# Setting error and exception to be handle by this class
#
handleLanguage(__FILE__); /*default*/
set_error_handler(array("ErrorHandler", "getErrorStatically"),E_ALL);
set_exception_handler(array("ErrorHandler", "getExceptionStatically"));

/**
 * handle errors
 *
 */
Class ErrorHandler extends ErrorException {
  /**
   * const that helps to identify an exception
   *
   */
  const TYPE_EXP = '1';

  /**
   * const that helps to identify an error
   *
   */
  const TYPE_ERR = '2';

  /**
   * textual type of error
   *
   * @var string
   */
  private $TypeOfError;

  /**
   * stores itselfs instance
   *
   * @var resource
   */
  protected static $Me;

  /**
   * show the exception if it has been called by throw new exception
   * else, it would be just an empty construct \ o /
   *
   * @param string $StMessage
   * @param int $ItCode
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  public function __construct( $StMessage = '',$ItCode = 0 ) {

    if ( ! empty($StMessage) ) {
      $this->_saveData(self::TYPE_EXP,$StMessage,$ItCode);
      $this->_getErrorAsHTML(true);
    }

  }

  /**
   * generates a html with the error message
   *
   * @param boolean $Print
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  public function _getErrorAsHTML($Print = false) {

    require_once(INCLUDESDIR . '/errorPage.php');

    $Trace = $this->_filterTrace($this->getTrace());
    $StType = ($this->TypeOfError == self::TYPE_EXP) ? 'EXP' : 'ERR';
    $StTitle = $this->getTitle();
    $StMessage = $this->getMessage();
    $ItSeverity = $this->getSeverity();
    $StFile = $this->getFile();
    $ItLine = $this->getLine();

    $Html = getErrorPageHTML($StType,$StTitle,$StMessage,$ItSeverity,$StFile,$ItLine);

    if ($Print) {
      die($Html);
    } else {
      return $Html;
    }

  }

  /**
   * get the title to the specific kind of error
   *
   * @return string
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  public function getTitle() {

    $ArTitle[self::TYPE_ERR] = ERR_ERROR_TITLE;
    $ArTitle[self::TYPE_EXP] = ERR_EXC_TITLE;

    return $ArTitle[$this->TypeOfError];

  }

  /**
   * get the saved notice and shows it
   *
   * @return string
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  public function getNotice() {
    $Html = getSessionProp('notice');
    setSessionProp('notice','');
    return $Html;

  }

  /**
   * set the first notice to be shown after
   *
   * @return bool
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  private function setNotice() {
    if (getSessionProp('notice') == '') {
      setSessionProp('notice',$this->_getErrorAsHTML());
    }

    return true;
  }

  /**
   * Saves the data of the exception/error
   *
   * @param int $Type
   * @param string $StMessage
   * @param int $ItCode
   * @param int $ItSeverity
   * @param string $StFile
   * @param int $ItLine
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  private function _saveData($Type,$StMessage = '',$ItCode = 0,$ItSeverity = 0,$StFile = '',$ItLine = 0 ) {

    if ($Type == self::TYPE_EXP)
      Exception::__construct($StMessage,$ItCode);
    else
      ErrorException::__construct($StMessage,$ItCode,$ItSeverity,$StFile,$ItLine);

    $this->TypeOfError = $Type;

  }

  /**
   * filter some kind of useless information
   *
   * @param array $ArTrace
   * @param array $Wanted
   * @return array
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  private function _filterTrace( $ArTrace,$Wanted = array() ) {

    $unWanted = array('GLOBALS','_ENV','HTTP_ENV_VARS','_POST','_GET','HTTP_POST_VARS',
                      'HTTP_GET_VARS','_COOKIE','HTTP_COOKIE_VARS','_SERVER','HTTP_SERVER_VARS',
                      '_FILES','HTTP_POST_FILES','_REQUEST');

    $unWanted = array_flip(array_diff($unWanted,$Wanted));

    foreach ($ArTrace as &$Trace) {
      if (isset($Trace['args'][4]) && is_array($Trace['args'][4])) {
        while ($Type = each($Trace['args'][4])) {
          $Chave = $Type['key'];
          if ( ! is_int($Chave) && isset($unWanted[$Chave]) ) { unset($Trace['args'][4][$Chave]); }
        }
      }
    }

    return $ArTrace;

  }

  /**
   * get admins email
   *
   * @return array
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  private function _getAdminsMail() {

    $ArAdmins = array();
    $DB = new DBHandler();
    $StSQL = '
SELECT
  US.StEmail AS StEmail
FROM
  ' . DBPREFIX . 'User US
  LEFT JOIN ' . DBPREFIX . 'Supporter S ON (US.IDUser = S.IDUser)
  LEFT JOIN ' . DBPREFIX . 'Unit UN ON (UN.IDUnit = S.IDUnit)
WHERE
  UN.BoMailError = true';

    $DB->execSQL($StSQL);
    $Admins = $DB->getResult('string');
    foreach ((array) $Admins as $Admin)
      $ArAdmins[] = $Admin['StEmail'];

    return $ArAdmins;

  }

  /**
   * send an email to admins alerting about an error
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   *
   */
  private function _adviseAdmins() {

    $ArAdmins = $this->_getAdminsMail();
    $Text = $this->_getErrorAsHTML(false);
    $MailHandler = new MailHandler();
    $MailHandler->setHTMLBody(true);
    if ( is_array($ArAdmins) )
      $MailHandler->sendMail($ArAdmins,ERR_MAIL_SUBJ,$Text,'content-type:text/html;utf-8');

  }

  /**
   * handle the exception when its catched by set_exception_handler
   *
   * @param resource $Exception
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  public static function getExceptionStatically( $Exception ) {

    $Error = ErrorHandler::getInternalInstance();
    $Error->_saveData(ErrorHandler::TYPE_EXP,$Exception->getMessage(),
                     $Exception->getCode(),1,$Exception->getFile(),$Exception->getLine());

  }

  /**
   * handle the error when its catched by set_error_handler
   *
   * @param resource $Exception
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  public static function getErrorStatically( $ItSeverity = 0,$StMessage = '',$StFile = '',$ItLine = 0 ) {

    $Error = ErrorHandler::getInternalInstance();
    $Error->_saveData(ErrorHandler::TYPE_ERR,$StMessage,0,$ItSeverity,$StFile,$ItLine);
    $Error->_adviseAdmins();

    if ($ItSeverity > 8) {
      $Error->_getErrorAsHTML(true);
    } else {
      $Error->setNotice();
    }

  }

  /**
   * function that helps to debug
   *
   * @param array $Array
   */
  public static function Debug( $Array ) {
    throw new ErrorHandler('<pre style="text-align:left">' . print_r($Array,1) . '</pre>');
  }

  /**
   * gets an instance of this class to use in static methods
   *
   * @return resource
   *
   * @author Dimitri Lameri <Contato@DimitriLameri.com>
   */
  protected static function getInternalInstance() {

    if ( ! is_resource(ErrorHandler::$Me) )
      ErrorHandler::$Me = new ErrorHandler();

    return ErrorHandler::$Me;

  }

}

?>