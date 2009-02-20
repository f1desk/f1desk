<?php

  handleLanguage('global.php');

  /**
   * include the language files
   *
   * @param string $StFilePath
   */
  function handleLanguage($StFilePath) {
    $Lang = getOption('lang');

    $ArFilePath = explode('/',$StFilePath);
    $StFile = end($ArFilePath);
    if (file_exists(LANGDIR . $Lang . '/lang.' . $StFile)) {
      require_once(LANGDIR . $Lang . '/lang.' . $StFile);
    } elseif (file_exists(ABSTEMPLATEDIR . 'lang/' . $Lang . '/lang.' . $StFile)) {
      require_once(ABSTEMPLATEDIR . 'lang/' . $Lang . '/lang.' . $StFile);
    }
  }

?>