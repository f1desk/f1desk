<?php
require_once('main.php');
handleLanguage(__FILE__);
if (isset($_POST) && !empty($_POST['StAction'])) {
  switch ($_POST['StAction']) {
    case 'insertMenu':
        if (!createOption('menu','menu_tabs',$_POST['StName'],array('id'=>$_POST['StAddress'])))
          ErrorHandler::setNotice(MENU_INSERT_ERR,'error');
    break;

    case 'removeMenu':
      if(! removeOption($_POST['IDMenu'],'id'))
        ErrorHandler::setNotice(MENU_REMOVE_ERR,'error');
    break;

    case 'editMenu':
      if (!setOption($_POST['StOldAddress'], array('id'=>addslashes($_POST['StAddress']),'text'=>addslashes($_POST['StName'])) ,'id'))
        ErrorHandler::setNotice(MENU_EDIT_ERR,'error');
    break;
  }
}


$ArMenus = F1DeskUtils::getMenuTab('admin');
$ArDepartments = F1DeskUtils::getPublicDepartments(false);
$ArSupporters = array();
foreach ($ArDepartments as $ArDepartment) {
  $ArSupporters[$ArDepartment['IDDepartment']] = F1DeskUtils::getDepartmentSupporters($ArDepartment['IDDepartment']);
}

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