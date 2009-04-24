<?php

  handleLanguage('global.php');

  /**
   * include the language files
   *
   * @param string $StFilePath
   */
  function handleLanguage($StFilePath, $Debug = false) {
    $Lang = getCurrentLanguage();
    $FilePath = '';

    $ArFilePath = preg_split('/[\/|\\\]/',$StFilePath);
    $StFile = end($ArFilePath);

    if (strpos($StFilePath,'templates') !== false && file_exists(ABSTEMPLATEDIR . 'lang/' . $Lang . '/lang.' . $StFile)) {
      $FilePath = ABSTEMPLATEDIR . 'lang/' . $Lang . '/lang.' . $StFile;
    } else if (file_exists(LANGDIR . $Lang . '/lang.' . $StFile)) {
      $FilePath = LANGDIR . $Lang . '/lang.' . $StFile;
    }

    if ($FilePath) {
      require_once($FilePath);
    }

    if ($Debug) {
      return $FilePath;
    }
  }

?>