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
 * create a XMLtag in option file
 *
 * @param string $StSetting
 * @param string $StValue
 * @return boolean
 */
function createOption($StParentName, $StSetting, $StValue, $ArAttributes = array() ) {
  $Dom = new DOMDocument();
  $Dom->load(dirname(__FILE__) . '/option.xml');

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
 * set options
 *
 * @param string $StSetting
 * @param string $StValue
 *
 * @return string
 */
function setOption($StSetting, $StValue) {
  $Dom = new DOMDocument();
  $Dom->load(dirname(__FILE__) . '/option.xml');

  $StSetting = strtolower($StSetting);
  $StValue = htmlspecialchars($StValue);

  $Dom->getElementsByTagName($StSetting)->item(0)->nodeValue = $StValue;

  if ( $Dom->save(dirname(__FILE__) . '/option.xml') ) {
    return true;
  } else {
    return false;
  }
}


function getElementByID($ID,$Node = '') {
  if ($Node == '') {
    $Dom = new DOMDocument();
    $Dom->load(dirname(__FILE__).'/option.xml');
    $Options = $Dom->getElementsByTagName('options')->item(0);
    $Children = $Options->childNodes;
    foreach ($Children as $Child) {
      if (! $Child instanceof DOMText)
          print "1 CHILD: {$Child->tagName} ID: $ID ATTR: {$Child->getAttribute('id')} RES:".($Child->getAttribute('id') == $ID)."<br>";
      if (! $Child instanceof DOMText && $Child->getAttribute('id') == $ID)
        return $Child;
      if ($Child->hasChildNodes()) {
        getElementByID($ID,$Child);
      }
    }
  } else {
    if ($Node instanceof DOMElement) {
      $Children = $Node->childNodes;
      foreach ($Children as $Child) {
        if (! $Child instanceof DOMText)
          print "2 ID: $ID ATTR: {$Child->getAttribute('id')} RES:".($Child->getAttribute('id') == $ID)."<br>";
        if (! $Child instanceof DOMText && $Child->getAttribute('id') == $ID)
          die($Child->getAttribute('id'));
          return $Child;
        if ($Child->hasChildNodes()) {
          getElementByID($ID,$Child);
        }
      }
    }
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
  $Dom->load(dirname(__FILE__).'/option.xml');
  if ($Mode == 'id') {
    $Node = getElementById($Item);
    if (!is_null($Node) && $Node instanceof DOMElement)
      $Node->parentNode->removeChild($Node);
    else
      return false;
  } else {
    $NLElements = $Dom->getElementsByTagName($Item);
    foreach ($NLElements as $NElement) {
      try {
        @$Dom->removeChild($NElement);
      } catch (Exception $Exc) {
        return false;
      }
    }
    return true;
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
	$ObDOM = getOption( "tag", "DOM" );
	foreach ($ObDOM as $item) {
		$StHTML .= ('<' . $item->nodeValue . '>');
	}
	return f1desk_strip_tags($toEscape, $StHTML);
}

function returnData($StReturnType, $StReturnURL){
  if ($StReturnType == 'redirect') {
  	header("Location: ".TEMPLATEDIR."$StReturnURL");
  } else {
    if (file_exists(TEMPLATEDIR.$StReturnURL))
      include_once(TEMPLATEDIR.$StReturnURL);
  }
}

?>