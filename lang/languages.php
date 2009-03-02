<?php

  handleLanguage('global.php');

  /**
   * include the language files
   *
   * @param string $StFilePath
   */
  function handleLanguage($StFilePath, $Debug = false) {
    $Lang = getOption('lang');
    $FilePath = '';

    $ArFilePath = preg_split('/[\/|\\\]/',$StFilePath);
    $StFile = end($ArFilePath);
    if (file_exists(LANGDIR . $Lang . '/lang.' . $StFile)) {
      $FilePath = LANGDIR . $Lang . '/lang.' . $StFile;
    } elseif (file_exists(ABSTEMPLATEDIR . 'lang/' . $Lang . '/lang.' . $StFile)) {
      $FilePath = ABSTEMPLATEDIR . 'lang/' . $Lang . '/lang.' . $StFile;
    }

    if ($FilePath) {
      require_once($FilePath);
    }

    if ($Debug) {
      return $FilePath;
    }
  }

?>