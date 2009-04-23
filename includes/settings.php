<?php
function getCurrentTemplate(){
  $Dom = new DOMDocument();
  $Dom->load( dirname(__FILE__) . '/option.xml');
  $StChoosenTemplate = $Dom->getElementsByTagName('avail_templates')->item(0)->getAttribute('choosen');
  return $Dom->getElementById($StChoosenTemplate)->nodeValue;
}

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
  $Dom->formatOutput = true;

  $StSetting = strtolower($StSetting);

  $Node = $Dom->getElementsByTagName($StSetting);

  if ( $StReturnType == "string" ) {
  	return $Node->item(0)->nodeValue;
  } else {
  	return $Node;
  }

}

/**
 * create a XMLtag in option file
 *
 * @param string $StSetting
 * @param string $StValue
 * @return boolean
 */
function createOption($StParentName, $StSetting, $StValue, $ArAttributes = array() ) {
  $Dom = new DOMDocument();
  $Dom->load(dirname(__FILE__) . '/option.xml');
  $Dom->formatOutput = true;

  $StSetting = strtolower($StSetting);
  $StValue = htmlspecialchars($StValue);
  $ObParent = $Dom->getElementsByTagName($StParentName)->item(0);

  #
  # Looking if this entry already exists
  #
  $BoExists = false;
  $tagCount = $ObParent->getElementsByTagName($StSetting)->length;
  for ($i=0; $i < $tagCount; $i++) {
    if ( $ObParent->getElementsByTagName($StSetting)->item($i)->nodeValue == $StValue ){
      $BoExists = true;
      break;
    }
  }

  if ( !$BoExists ) { ## Do not exists
  	$Element = $Dom->createElement($StSetting);
    $ObParent->appendChild($Element);

    $ElementText = $Dom->createTextNode($StValue);
    $Element->appendChild($ElementText);

    #
    # Setting Attributes given
    #
    if (is_array($ArAttributes) && count($ArAttributes)!=0){
      foreach ($ArAttributes as $StAttributeName => $StAttributeValue) {
        $StAttributeName = ($StAttributeName == 'id') ? 'xml:id' : $StAttributeName;
        Validate::NCName($StAttributeName);
        Validate::NCName($StAttributeValue);
        $Element->setAttribute($StAttributeName, $StAttributeValue);
      }
    }

    if ( $Dom->save(dirname(__FILE__) . '/option.xml') ) {
      return true;
    } else {
      return false;
    }
  } else { ## Already exists
    return false;
  }

}

/**
 * Remove an option
 *
 * @param string $Item
 * @param string $Mode
 * @return boolean
 */
function removeOption($Item, $Mode = 'name') {
  $Dom = new DOMDocument();
  $Dom->load(dirname(__FILE__) . '/option.xml');
  $Dom->formatOutput = true;

  if ($Mode == 'id') {
    $Node = $Dom->getElementById($Item);
    if ($Node) {
      $Node->parentNode->removeChild($Node);
    } else {
      return false;
    }
  } else {
    $ElementList = $Dom->getElementsByTagName($Item);
    foreach ($ElementList as $NodeElement) {
      if (! $NodeElement->parentNode->removeChild($NodeElement)) {
        return false;
      }

    }
  }

  $Dom->save(dirname(__FILE__) . '/option.xml');
  return true;
}

/**
 * set options
 *
 * @param string $StSetting
 * @param string $StValue
 *
 * @return string
 */
function setOption($StSetting, $ArValues = array(), $Mode = 'name') {
  $Dom = new DOMDocument();
  $Dom->load(dirname(__FILE__) . '/option.xml');
  $Dom->formatOutput = true;

  $StSetting = strtolower($StSetting);

  if ($Mode == 'id') {
    $Node = $Dom->getElementById($StSetting);
    if (is_null($Node)) {
      return false;
    }

    foreach ($ArValues as $Attr => $Value) {
      if ($Attr == 'text') {
        $Node->nodeValue = htmlspecialchars($Value);
      } else {
        $Attr = ($Attr == 'id') ? 'xml:id' : $Attr;
        $Value = htmlspecialchars($Value);
        Validate::NCName($Attr);
        Validate::NCName($Value);
        $Node->setAttribute($Attr,$Value);
      }
    }
  } else {
    foreach ($ArValues as $Attr => $Value) {
      $Value = htmlspecialchars($Value);
      Validate::NCName($Value);
      if ($Attr == 'text') {
        $Dom->getElementsByTagName($StSetting)->item(0)->nodeValue = $Value;
      } else {
        $Attr = ($Attr == 'id') ? 'xml:id' : $Attr;
        Validate::NCName($Attr);
        $Dom->getElementsByTagName($StSetting)->item(0)->setAttribute($Attr,$Value);
      }
    }
  }

  if ($Dom->save(dirname(__FILE__) . '/option.xml')) {
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
  $ArDefaultJs = array('utils','libAjax','DragNdrop','Flow', 'global');

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
	if ( array_key_exists($StProp,$_SESSION) ) {
		return  $_SESSION[$StProp];
	} else {
		return "";
	}
}

/**
 * unsets a propertie from the session
 *
 * @param string $StProp
 */
function unsetSessionProp( $StProp ) {
  if ( array_key_exists($StProp,$_SESSION) ) {
		unset($_SESSION[$StProp]);
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
	$_SESSION[$StProp] = $StValue;
	return $StValue;
}

function f1desk_escape_string($toEscape, $nl2br = false, $BoEncode = false) {
	if ( is_array( $toEscape ) ) {
		foreach ( $toEscape as &$scape ){
			$scape = f1desk_escape_string( $scape );
		}
		return $toEscape;
	} else {
	  if ($nl2br)
			$toEscape = (str_replace("\n", "<br />",$toEscape));
		if ($BoEncode) {
  		$toEscape = (str_replace("'", "%27", $toEscape));
      $toEscape = (str_replace('"', "%22", $toEscape));
      $toEscape = (str_replace(' ', "%20", $toEscape));
		} else {
		  $toEscape = mysql_escape_string( $toEscape );
		}
		return $toEscape;
	}
}

function f1desk_strip_tags($TxMessage, $StHTML) {
  $ArMessages=explode('<',$TxMessage);  $StResult=$ArMessages[0];
  for($i=1;$i<count($ArMessages);$i++){
    if(!strpos($ArMessages[$i],'>'))
      $StResult = $StResult.'&lt;'.$ArMessages[$i];
    else
      $StResult = $StResult.'<'.$ArMessages[$i];
  }
  return strip_tags($StResult, $StHTML);
}

function f1desk_escape_html($toEscape){
  $StHTML = "";
	$ObDOM = getOption( 'tag', 'node' );
	foreach ($ObDOM as $item) {
	  $StHTML .= sprintf('<%s>', $item->nodeValue);
	}
	return f1desk_strip_tags($toEscape, $StHTML);
}

?>