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
    throw new Exception("N&atilde;o foi poss&iacute;vel carregar a classe: $StClass");
  }

}

?>