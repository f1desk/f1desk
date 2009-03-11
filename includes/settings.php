<?php

/**
 * get options
 *
 * @param string $StSetting
 * @param string $StReturnType
 *
 * @return string
 */
function getOption($StSetting, $StReturnType = "string") {
  $Dom = new DOMDocument();
  $Dom->load(dirname(__FILE__) . '/option.xml');
  $StSetting = strtolower($StSetting);
  if ( $StReturnType == "string" ) {
  	return $Dom->getElementsByTagName($StSetting)->item(0)->nodeValue;
  } else {
  	return $Dom->getElementsByTagName($StSetting);
  }

}

/**
 * set options
 *
 * @param string $StSetting
 * @param string $StValue
 *
 * @return string
 */
function setOption($StSetting, $StValue) {
  $Dom = new DOMDocument();
  $Dom->load(dirname(__FILE__) . 'option.xml');

  $StSetting = strtolower($StSetting);
  $StValue = htmlspecialchars($StValue);

  $Dom->getElementsByTagName($StSetting)->item(0)->nodeValue = $StValue;

  if ( $Dom->save('option.xml') ) {
    return true;
  } else {
    return false;
  }
}

/**
 * outputs the default header JS
 *
 * @return string
 */
function defaultJS() {
  $Html = '';
  $ArDefaultJs = array('json2','utils','libAjax','DragNdrop','Flow', 'global');

  foreach ($ArDefaultJs as $JsFile) {
    $Html .= '<script type="text/javascript" src="' . JSDIR . $JsFile . '.js"></script>' . "\n";
  }

  return $Html;
}

/**
 * outputs the default header CSS
 *
 * @return string
 */
function defaultCSS() {
  $Html = '';
  $ArDefaultCss = array('Flow');

  foreach ($ArDefaultCss as $CssFile) {
  	$Html .= '<link rel="stylesheet" type="text/css" href="' . CSSDIR . $CssFile .'.css">' . "\n";
  }

  return $Html;
}

/**
 * get a propertie from the session
 *
 * @param string $StProp
 * @return mixed
 */
function getSessionProp( $StProp ){
	if ( isset( $_SESSION[ $StProp ] ) ) {
		return  $_SESSION[ $StProp ];
	} else {
		return "";
	}
}

/**
 * set a propertie on the session
 *
 * @param string $StProp
 * @param mixed $StValue
 * @return mixed
 */
function setSessionProp( $StProp, $StValue ){
	$_SESSION[ $StProp ] = $StValue;
	return $StValue;
}

function f1desk_escape_string( $toEscape, $nl2br = false ){
	if ( is_array( $toEscape ) ) {
		foreach ( $toEscape as &$scape ){
			$scape = f1desk_escape_string( $scape );
		}
		return $toEscape;
	} else {
		if ( $nl2br ) 
			$toEscape = (str_replace("\n", "<br>",$toEscape));
		$toEscape = (str_replace("'", "%27", $toEscape));
		$toEscape = (str_replace('"', "%22", $toEscape));
		$toEscape = (str_replace(' ', "%20", $toEscape));
		return $toEscape;
	}
}

function f1desk_real_escape_string( $toEscape, $nl2br = false ) {
	if ( is_array( $toEscape ) ) {
		foreach ( $toEscape as &$scape ){
			$scape = f1desk_real_escape_string( $scape );
		}
		return $toEscape;
	} else {
		if ( $nl2br ) 
			$toEscape = (str_replace("\n", "<br>",$toEscape));
		$toEscape = mysql_real_escape_string( $toEscape );
		return $toEscape;
	}
}

?>