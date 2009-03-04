<?php
require_once('main.php');
$IDResponse = $_POST['IDResponse'];
$IDSupporter = $_POST['IDSupporter'];
$IDDepartment = $_POST['IDDepartment'];
$ArResponses = F1DeskUtils::listCannedResponses($IDSupporter,$IDDepartment);
$StAlias = ''; $Arbla = array();
foreach ($ArResponses as $Response) {
  if($Response['IDCannedResponse'] == $IDResponse) {
    $StMessage = $Response['StAlias'];
  }
}
die($StMessage);
?>