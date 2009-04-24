<?php
require_once('main.php');
handleLanguage(__FILE__);
if (isset($_POST) && !empty($_POST['StAction'])) {
  switch ($_POST['StAction']) {
    case 'insertMenu':
      if (!createOption('menu','menu_tabs',$_POST['StName'],array('id'=>$_POST['StAddress'])))
        ErrorHandler::setNotice('menu',MENU_INSERT_ERR,'error');
    break;

    case 'removeMenu':
      if(! removeOption($_POST['IDMenu'],'id'))
        ErrorHandler::setNotice('menu',MENU_REMOVE_ERR,'error');
    break;

    case 'editMenu':
      if (!setOption($_POST['StOldAddress'], array('id'=>addslashes($_POST['StAddress']),'text'=>addslashes($_POST['StName'])) ,'id'))
        ErrorHandler::setNotice('menu',MENU_EDIT_ERR,'error');
    break;

    case 'editDepartment':
      if (!isset($_POST['IDDepartment'])){
      	ErrorHandler::setNotice('department',NO_EDIT_ID,'error');
      } else {
        $ArData = array(
          'StDepartment' => f1desk_escape_string($_POST['StDepartment']),
          'StDescription' => f1desk_escape_string($_POST['StDescription'])
        );
        $ItAffedcted = F1DeskUtils::editDepartment($_POST['IDDepartment'], $ArData);
        if(!$ItAffedcted){
          ErrorHandler::setNotice('department', DEPTO_EDIT_ERROR,'error');
        } else {
          ErrorHandler::setNotice('department', DEPTO_EDIT_OK,'ok');
        }
      }
    break;

    case 'removeDepartment':
      if (!isset($_POST['IDDepartment'])){
      	ErrorHandler::setNotice('department', NO_EDIT_ID,'error');
      } else {
        $ItAffedcted = F1DeskUtils::removeDepartment($_POST['IDDepartment']);
        if(!$ItAffedcted){
          ErrorHandler::setNotice('department', DEPTO_REMOVE_ERROR,'error');
        } else {
          ErrorHandler::setNotice('department', DEPTO_REMOVE_OK,'ok');
        }
      }
    break;

    case 'createDepartment':
      if (!isset($_POST['IDSubDepartment']) || $_POST['IDSubDepartment'] == ""){ $_POST['IDSubDepartment'] = null; }
      if (!isset($_POST['TxSign'])){ $_POST['TxSign'] = ''; }
      $ItNewID = F1DeskUtils::createDepartment( f1desk_escape_string($_POST['StDepartment']), f1desk_escape_string($_POST['StDescription']), f1desk_escape_string($_POST['TxSign']), $_POST['IDSubDepartment']);
      if(!$ItNewID){
        ErrorHandler::setNotice('department',DEPTO_CREATE_ERROR,'error');
      } else {
        ErrorHandler::setNotice('department',DEPTO_CREATE_OK,'ok');
      }
    break;

    case 'createUnit':
      function validateBooleanPost( $StPost ){
        if ( isset($_POST[$StPost]) && ( $_POST[$StPost]===true || $_POST[$StPost] == '1' ) ) {
        	return '1';
        } else {
          return '0';
        }
      }
      $ItNewID = F1DeskUtils::createUnit( f1desk_escape_string( $_POST['StUnit'] ), array(
        "BoAnswer" => validateBooleanPost('BoAnswer'),
        "BoAttachTicket" => validateBooleanPost('BoAttachTicket'),
        "BoCreateTicket" => validateBooleanPost('BoCreateTicket'),
        "BoDeleteTicket" => validateBooleanPost('BoDeleteTicket'),
        "BoViewTicket" => validateBooleanPost('BoViewTicket'),
        "BoReleaseAnswer" => validateBooleanPost('BoReleaseAnswer'),
        "BoMailError" => validateBooleanPost('BoMailError'),
        "BoCannedResponse" => validateBooleanPost('BoCannedResponse')
      ) );
      if(!$ItNewID){
        ErrorHandler::setNotice('unit',UNIT_CREATE_ERROR,'error');
      } else {
        ErrorHandler::setNotice('unit',UNIT_CREATE_OK,'ok');
      }
    break;

    case 'editUnit':
      function validateBooleanPost( $StPost ){
        if ( $_POST[$StPost]===true || $_POST[$StPost] == '1' ) {
        	return '1';
        } else {
          return '0';
        }
      }
      if (!isset($_POST['IDUnit'])){
      	ErrorHandler::setNotice('unit', NO_EDIT_ID,'error');
      } else {
        $IDUnit = $_POST['IDUnit'];
        $ArData = array(
          'StUnit' => f1desk_escape_string($_POST['StUnit']),
          'BoAnswer' => validateBooleanPost('BoAnswer'),
          'BoAttachTicket' => validateBooleanPost('BoAttachTicket'),
          'BoCreateTicket' => validateBooleanPost('BoCreateTicket'),
          'BoDeleteTicket' => validateBooleanPost('BoDeleteTicket'),
          'BoViewTicket' => validateBooleanPost('BoViewTicket'),
          'BoReleaseAnswer' => validateBooleanPost('BoReleaseAnswer'),
          'BoMailError' => validateBooleanPost('BoMailError'),
          'BoCannedResponse' => validateBooleanPost('BoCannedResponse')
        );
        $ItAffedcted = F1DeskUtils::editUnit($IDUnit, $ArData);
        if(!$ItAffedcted){
          ErrorHandler::setNotice('unit',UNIT_EDIT_ERROR,'error');
        } else {
          ErrorHandler::setNotice('unit',UNIT_EDIT_OK,'ok');
        }
      }
    break;

    case 'removeUnit':
      if (!isset($_POST['IDUnit'])){
      	ErrorHandler::setNotice('unit', NO_EDIT_ID,'error');
      } else {
        $ItAffedcted = F1DeskUtils::removeUnit($_POST['IDUnit']);
        if(!$ItAffedcted){
          ErrorHandler::setNotice('unit',UNIT_REMOVE_ERROR,'error');
        } else {
          ErrorHandler::setNotice('unit',UNIT_REMOVE_OK,'ok');
        }
      }
    break;
    
    case 'editOption':
      if (!isset($_POST['StOption'])) {
      	ErrorHandler::setNotice('option', NO_EDIT_ID,'error');
      } else {
        if (!F1DeskUtils::editOption($_POST['StOption'], $_POST['StValue'])) {
        	ErrorHandler::setNotice('option', ERROR_EDIT_OPTION,'error');
        } else {
          ErrorHandler::setNotice('option', SUCESS_EDIT_OPTION,'ok');
        }
      }
    break;
    
    case 'setCurrentTemplate':
      if (!isset($_POST['StTemplateName'])) {
      	ErrorHandler::setNotice('template', NO_ID_SET_TEMPLATE,'error');
      } else {
        if ( !F1DeskUtils::setCurrentTemplate($_POST['StTemplateName']) ) {
        	ErrorHandler::setNotice('template', ERROR_SET_TEMPLATE,'error');
        } else {
          ErrorHandler::setNotice('template', SUCESS_SET_TEMPLATE,'ok');
        }
      }
    break;
    
    case 'createTemplate':
      if ( !F1DeskUtils::createTemplate($_POST['StName'], $_POST['StPath'], $_POST['StThumbnail'], $_POST['TxDescription']) ) {
      	ErrorHandler::setNotice('template', ERROR_CREATE_TEMPLATE,'error');
      } else {
        ErrorHandler::setNotice('template', SUCESS_CREATE_TEMPLATE,'ok');
      }
    break;
    
    case 'removeTemplate':
      if (!isset($_POST['StTemplateName'])) {
      	ErrorHandler::setNotice('template', NO_ID_SET_TEMPLATE,'error');
      } else {
        if ( !F1DeskUtils::removeTemplate($_POST['StTemplateName']) ) {
        	ErrorHandler::setNotice('template', ERROR_REMOVE_TEMPLATE,'error');
        } else {
          ErrorHandler::setNotice('template', SUCESS_REMOVE_TEMPLATE,'ok');
        }
      }
    break;

    case 'editLanguage':
      if (!isset($_POST['StTitle']) || !isset($_POST['StPath'])) {
      	ErrorHandler::setNotice('language', DATA_NEEDED_TO_LANGUAGE,'error');
      } else {
        if ( !F1DeskUtils::editLanguage($_POST['StTitle'], $_POST['StPath']) ) {
        	ErrorHandler::setNotice('language', ERROR_EDIT_LANGUAGE,'error');
        } else {
          ErrorHandler::setNotice('language', SUCESS_EDIT_LANGUAGE,'ok');
        }
      }
    break;
    
    case 'removeLanguage':
      if (!isset($_POST['StPath'])) {
      	ErrorHandler::setNotice('language', DATA_NEEDED_TO_LANGUAGE,'error');
      } else {
        if ( !F1DeskUtils::removeLanguage($_POST['StPath']) ) {
        	ErrorHandler::setNotice('language', ERROR_REMOVE_LANGUAGE,'error');
        } else {
          ErrorHandler::setNotice('language', SUCESS_REMOVE_LANGUAGE,'ok');
        }
      }
    break;
    
    case 'setCurrentLanguage':
      if (!isset($_POST['StPath'])) {
      	ErrorHandler::setNotice('language', DATA_NEEDED_TO_LANGUAGE,'error');
      } else {
        if ( !F1DeskUtils::setCurrentLanguage($_POST['StPath']) ) {
        	ErrorHandler::setNotice('language', ERROR_SET_LANGUAGE,'error');
        } else {
          ErrorHandler::setNotice('language', SUCESS_SET_LANGUAGE,'ok');
        }
      }
    break;
    
    case 'createLanguage':
      if (!isset($_POST['StTitle']) || !isset($_POST['StPath'])) {
      	ErrorHandler::setNotice('language', DATA_NEEDED_TO_LANGUAGE,'error');
      } else {
        if ( !F1DeskUtils::createLanguage($_POST['StTitle'], $_POST['StPath']) ) {
        	ErrorHandler::setNotice('language', ERROR_CREATE_LANGUAGE,'error');
        } else {
          ErrorHandler::setNotice('language', SUCESS_CREATE_LANGUAGE,'ok');
        }
      }
    break;

  }
}

$ArMenus = F1DeskUtils::getMenuTab('admin');
$ArDepartments = F1DeskUtils::getPublicDepartments(false);
$ArGeneralOptions = F1DeskUtils::listGeneralOptions();
$ArTemplates = F1DeskUtils::getTemplates();
$ArLanguages = F1DeskUtils::getLanguages();
$ArSupporters = array();
foreach ($ArDepartments as $ArDepartment) {
  $ArSupporters[$ArDepartment['IDDepartment']] = F1DeskUtils::getDepartmentSupporters($ArDepartment['IDDepartment']);
}
$ArUnits = F1DeskUtils::listUnits();
if (F1DeskUtils::isSupporter()) {
  $BoCreate = F1DeskUtils::getPermission('BoCreateTicket',getSessionProp('IDSupporter'));
  if ($BoCreate) {
    $ArDepartments = F1DeskUtils::getPublicDepartments(false);
  } else {
    $ArDepartments = F1DeskUtils::getDepartmentsFormatted(getSessionProp('IDSupporter'));
  }

} else {
  $ArDepartments = F1DeskUtils::getPublicDepartments();
}
?>