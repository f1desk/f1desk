<?php

/**
 * function that includes files of classes
 *
 * @param string $StClass
 */
function __autoload($StClass) {

  handleLanguage($StClass . '.php');

  require_once(CLASSDIR . $StClass . '.php');

  if (! class_exists($StClass, false)) {
    trigger_error("N&atilde;o foi poss&iacute;vel carregar a classe: $StClass", E_USER_WARNING);
  }

}

?>