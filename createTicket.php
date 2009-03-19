<?php
if (getSessionProp('isSupporter') == 'true') {
  $BoCreate = F1DeskUtils::getPermission('BoCreateCall',getSessionProp('IDSupporter'));
  if ($BoCreate) {
    $ArDepartments = TemplateHandler::getPublicDepartments(false);
  } else {
    $ArDepartments = TemplateHandler::getDepartments(getSessionProp('IDSupporter'));
  }
} else {
  $ArDepartments = TemplateHandler::getPublicDepartments();
}

$ArPriorities = F1DeskUtils::listPriorities();
$ArCategories = F1DeskUtils::listCategories();
if (TemplateHandler::IsSupporter()) {
  $ArSub = F1DeskUtils::getSubDepartments(getSessionProp('IDSupporter'));
}
?>