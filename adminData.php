<?php
require_once('main.php');
handleLanguage(__FILE__);
if (isset($_POST) && !empty($_POST['StAction'])) {
  switch ($_POST['StAction']) {
    case 'insertMenu':
      try {
        createOption('menu','menu_tabs',$_POST['StName'],array('id'=>$_POST['StAddress']));
      } catch (Exception $Exc) {
        ErrorHandler::setNotice(MENU_INSERT_ERR,'error');
      }
    break;
    case 'removeMenu':
      if(! removeOption($_POST['IDMenu'],'id'))
        ErrorHandler::setNotice(MENU_REMOVE_ERR,'error');
    break;
  }
}
$ArMenus = F1DeskUtils::getMenuTab('admin');
?>