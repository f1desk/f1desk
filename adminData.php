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
      if (!setOption($_POST['StOldAddress'], array('id'=>$_POST['StAddress'],'text'=>$_POST['StName']) ,'id'))
        ErrorHandler::setNotice('menu',MENU_EDIT_ERR,'error');
    break;

    case 'editDepartment':
      if (!isset($_POST['IDDepartment']))
      	ErrorHandler::setNotice('department',NO_EDIT_ID,'error');
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
    break;
    
    case 'removeDepartment':
      if (!isset($_POST['IDDepartment']))
      	ErrorHandler::setNotice(NO_EDIT_ID,'error');
      $ItAffedcted = F1DeskUtils::removeDepartment($_POST['IDDepartment']);
      if(!$ItAffedcted){
        ErrorHandler::setNotice('department', DEPTO_REMOVE_ERROR,'error');
      } else {
        ErrorHandler::setNotice('department', DEPTO_REMOVE_OK,'ok');
      }
    break;
    
    case 'createDepartment':
      if (!isset($_POST['IDSubDepartment']) || $_POST['IDSubDepartment'] == ""){ $_POST['IDSubDepartment'] = null; }
      if (!isset($_POST['TxSign'])){ $_POST['TxSign'] = ''; }
      $ItNewID = F1DeskUtils::createDepartment($_POST['StDepartment'], $_POST['StDescription'], $_POST['TxSign'], $_POST['IDSubDepartment']);
      if(!$ItNewID){
        ErrorHandler::setNotice('department',DEPTO_CREATE_ERROR,'error');
      } else {
        ErrorHandler::setNotice('department',DEPTO_CREATE_OK,'ok');
      }
    break;
  }
}


$ArMenus = F1DeskUtils::getMenuTab('admin');

if (F1DeskUtils::isSupporter()) {

  $BoCreate = F1DeskUtils::getPermission('BoCreateCall',getSessionProp('IDSupporter'));
  if ($BoCreate) {
    $ArDepartments = F1DeskUtils::getPublicDepartments(false);
  } else {
    $ArDepartments = F1DeskUtils::getDepartmentsFormatted(getSessionProp('IDSupporter'));
  }

} else {
  $ArDepartments = F1DeskUtils::getPublicDepartments();
}
?>