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
        ErrorHandler::setNotice('department',REQUEST_OK,'error');
      } else {
        ErrorHandler::setNotice('department',REQUEST_ERROR,'ok');
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