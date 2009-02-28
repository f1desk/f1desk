<?php
require_once( dirname(__FILE__) . '/../includes/autoLoad.php');
class MailHandler {

  private $StMailHost;
  private $StMailUser;
  private $StMailPass;
  private $StMailFrom;
  private $StFromName;
  private $BoHTMLBody;
  private $StReplyTo;
  private $ObjMail;

  public function __construct() {
    $this->ObjMail = new PHPMailer();
    $this->ObjMail->IsSMTP();

    $this->StMailHost = MAILHOST;
    $this->StMailUser = MAILUSER;
    $this->StMailPass = MAILPASS;
    $this->StMailFrom = MAILFROM;

    $this->ObjMail->ClearAddresses();
    $this->ObjMail->ClearAllRecipients();
    $this->ObjMail->ClearAttachments();
    $this->ObjMail->ClearBCCs();
    $this->ObjMail->ClearCCs();
    $this->ObjMail->ClearCustomHeaders();
    $this->ObjMail->ClearReplyTos();
  }

  public function getMailHost() {
    return $this->StMailHost;
  }

  public function setMailHost($StMailHost) {
    $this->StMailHost = $StMailHost;
  }

  public function getMailUser() {
    return $this->StMailUser;
  }

  public function setMailUser($StMailUser) {
    $this->StMailHost = $StMailUser;
  }

  public function getMailPass() {
    return $this->StMailPass;
  }

  public function setMailPass($StMailPass) {
    $this->StMailHost = $StMailPass;
  }

  public function getFromName() {
    return $this->StFromName;
  }

  public function setFromName($StFromName) {
    $this->StMailHost = $StFromName;
  }

  public function isHTML(){
    return $this->BoHTMLBody;
  }

  public function setHTMLBody($BoValue) {
    $this->BoHTMLBody = $BoValue;
  }

  public function getReplyTo() {
    return $this->StReplyTo;
  }

  public function setReplyTo($StEmail) {
    $this->StReplyTo = $StEmail;
  }

  public function attachFile($StFile,$StName="") {
    if (empty($StFile)) {
      throw new ErrorHandler(EXC_MAIL_FPATH);
    }
    $this->ObjMail->addAttachment ($StFile,$StName);
  }

  private function createHeaders($StHeaders) {
    $StCharset = '';
    if (empty($StHeaders)) {
      $StHeaders = array();
    }
    elseif (! array($StHeaders)){
      $ArTempHeaders = (array) explode("\n",$StHeaders);
      $StHeaders = array();
      if (!empty($ArTempHeaders)) {
        foreach ($ArTempHeaders as $StTempHeader) {
          if (strpos($StTempHeader,":") === false)
            continue;
          list($StName,$StValue) = explode(':',$StTempHeader,2);
          if (strtolower($StName) == 'from') {
            if(strpos($content, '<' ) !== false) {
              $StFromName = substr($StValue, 0, strpos($StValue, '<')- 1);
              $StFromName = str_replace('"', '', $StFromName);
              $StFromName = trim($StFromName);

              $StFromEmail = substr($StValue, strpos($StValue, '<')+ 1);
              $StFromEmail = str_replace('>', '', $StFromEmail);
              $StFromEmail = trim($StFromEmail);
            }
            else {
              $StFrom = trim($StValue);
            }
          }
          elseif (strtolower($StName) == 'content-type') {
            if (strpos($StValue,';') !== false) {
              list($StType,$StCharset) = explode(';',$StValue);
              $StContentType = strtolower(trim($StType));
              $StCharset = trim(str_replace(array( 'charset=', '"' ), '', $StCharset));
            }
            else {
              $StContentType = trim($StValue);
            }
          }
          else {
            $ArHeaders[trim($StName)] = trim($StValue);
          }
        }
      }
    }

    if (empty($StContentType))
      $StContentType = 'text/plain';

    if ($StContentType == 'text/html')
      $this->setHTMLBody(true);

    $this->ObjMail->CharSet = $StCharset;

    if (! empty($ArHeaders)) {
      foreach ($ArHeaders as $StHeader) {
        $this->ObjMail->addCustomHeader(sprintf('%1$s: %2$s', $name, $content));
      }
    }

    return true;
  }

  public function addCc($StAddress, $StName = "") {
    if (empty($StAddress))
    	throw new ErrorHandler(EXC_MAIL_RQDEMAIL);

    if(!preg_match('/[^ ]@.{3}(\..{2})?/',$StDest))
      throw new ErrorHandler(EXC_MAIL_INVALID);

    $this->ObjMail->addCc($StAddress, $StName);
    return true;
  }

  public function addBcc($StAdress, $StName = "") {
    if (empty($StAdress))
      throw new ErrorHandler(EXC_MAIL_RQDEMAIL);

    if(!preg_match('/[^ ]@.{3}(\..{2})?/',$StDest))
      throw new ErrorHandler(EXC_MAIL_INVALID);

      $this->ObjMail->addBcc($StAdress,$StName);
      return true;
  }

  public function sendMail($StDest,$StSubject,$StBody,$ArHeaders = array()) {
    if (empty($this->StMailHost) || empty($this->StMailUser) || empty($this->StMailPass) || empty($this->StMailFrom)) {
      throw new ErrorHandler(EXC_MAIL_SETINFO);
    }

    $this->ObjMail->Host = $this->StMailHost;
    $this->ObjMail->SMTPAuth = true;
    $this->ObjMail->Username = $this->StMailUser;
    $this->ObjMail->Password = $this->StMailPass;
    $this->ObjMail->From = $this->StMailFrom;
    $this->ObjMail->FromName = $this->StFromName;

    if (is_array($StDest)) {
      foreach ($StDest as $Dest) {

        if(!preg_match('/[^ ]@.{3}(\..{2})?/',$Dest)) {
          throw new ErrorHandler(EXC_MAIL_INVALID);
        }

        $this->ObjMail->AddAddress($Dest,'');
      }
    }
    else {
      $this->ObjMail->AddAddress($StDest,'');
    }

    $this->ObjMail->AddReplyto($this->StReplyTo,'');
    $this->ObjMail->isHTML($this->BoHTMLBody);
    $this->ObjMail->Subject = $StSubject;
    $this->ObjMail->Body = $StBody;

    if (! empty($ArHeaders))
      $this->createHeaders($ArHeaders);

    return $this->ObjMail->Send();
  }

  public function __destruct() {
//    unset($this->ObjMail);
  }
}