<?php

/**
 * class for data validation
 *
 * @author Dimitri Lameri <contato@DimitriLameri.com>
 */
abstract class Validate {

  /**
   * center the validation
   *
   * @param string $StPattern
   * @param string $StData
   * @param string $StException
   * @return bool
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function doValidation($StPattern,$StData,$StException) {
    if (preg_match($StPattern,$StData)) {
      return true;
    } else {
      throw new ErrorHandler($StException);
    }
  }

  /**
   * validate email
   *
   * @param string $StEmail
   * @return bool
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function Email($StData) {
    $ExpReg = '/^[\w\.\-]+[@][\w\.\-]+\.(?i)[a-z\_\-]{2,4}$/';
    return self::doValidation($ExpReg,$StData,EXC_INVALID_EMAIL);
  }

  /**
   * validate hex
   *
   * @param string $StData
   * @return bool;
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function Hex($StData) {
    $ExpReg = '/^#?(((?i)[a-z]|[0-9]){3}(((?i)[a-z]|[0-9]){3})?)$/';
    return self::doValidation($ExpReg,$StData,EXC_INVALID_HEX);
  }

  /**
   * validate date
   *
   * @param string $StData
   * @return bool;
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function Date($StData) {
    $StSeparator = '(\/|\-)';
    $StDay = '([0-2][0-9]|3[0-1])';
    $StMonth = '(0[1-9]|1[0-2])';
    $StYear = '([0-9]{4})';

    $ExpReg = preg_replace('/\/|\-/',$StSeparator,DATE_FORMAT);
    $ExpReg = preg_replace('/[d]/i',$StDay,$ExpReg);
    $ExpReg = preg_replace('/[m]/i',$StMonth,$ExpReg);
    $ExpReg = preg_replace('/[y]/i',$StYear,$ExpReg);

    $ExpReg = '/^' . $ExpReg . '$/';

    return self::doValidation($ExpReg,$StData,EXC_INVALID_DATE);
  }

  /**
   * validate time
   *
   * @param string $StData
   * @return bool;
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function Time($StData) {
    $StSeparator = '(\/|\-|\:)';
    $StHour = '([01][0-9]|2[0-3])';
    $StMinute = '([0-5][0-9])';
    $StSecond = '([0-5][0-9])';

    $ExpReg = preg_replace('/\/|\-|\:/',$StSeparator,DATE_FORMAT);
    $ExpReg = preg_replace('/[h]/i',$StHour,$ExpReg);
    $ExpReg = preg_replace('/[i]/i',$StMinute,$ExpReg);
    $ExpReg = preg_replace('/[s]/i',$StSecond,$ExpReg);

    $ExpReg = '/^' . $ExpReg . '$/';

    return self::doValidation($ExpReg,$StData,EXC_INVALID_TIME);
  }

  /**
   * validate domain
   *
   * @param string $StData
   * @return bool
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function Domain($StData) {
    $ExpReg = '/^[\w\.\-]+\.(?i)[a-z\_\-]{2,4}$/';
    return self::doValidation($ExpReg,$StData,EXC_INVALID_DOMAIN);
  }

  /**
   * validate IP
   *
   * @param string $StData
   * @return bool
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function IP($StData) {
    $ExpReg = '/^(([0-9]{1,3})\.){4}$/';
    return self::doValidation($ExpReg,$StData,EXC_INVALID_IP);
  }

  /**
   * validate users session
   *
   * @param bool $Return
   *
   * @return bool
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function Session($Return = false) {

    $Valid = true;

    if (!(array_key_exists('StHash',$_SESSION) && array_key_exists('IDUser',$_SESSION) && array_key_exists('StName',$_SESSION)) ) {
      $Valid = false;
    }

    if ($Valid === true) {
      $StHash = $_SESSION['StHash'];
      $StComparison = md5($_SESSION['IDUser'] . $_SESSION['StName']);

      if ($StHash !== $StComparison) {
        $Valid = false;
      }
    }

    if ($Return == true || $Valid === true) {
      return $Valid;
    } else if ($Valid === false) {
      session_destroy();
      if (array_key_exists('page',$_GET)) {
        session_start();
        setSessionProp('lastPage',$_GET['page']);
      }
      F1DeskUtils::showPage('login');
    	die();
    }
  }

  /**
   * validate if a user is external
   *
   * @return bool
   *
   * @author Dimitri Lameri <contato@DimitriLameri.com>
   */
  public static function ExternalUser() {
    if (! ISEXTERNAL) {
      throw new ErrorHandler(EXC_INVALID_EXTERNALUSER);
    }
  }

}

?>