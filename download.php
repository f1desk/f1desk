<?php
require_once('main.php');
Validate::Session();
if(isset($_GET['IDAttach'])) {
  $IDAttachment = $_GET['IDAttach'];
}
$TicketHandler = new TicketHandler();
UserHandler::SQLInjectionHandle($IDAttachment);
$ID = getSessionProp('IDUser');
$ArResult = $TicketHandler->canDownload($IDAttachment, $ID);
if (F1DeskUtils::isSupporter()) {
  $ArResult['BoPermission'] = 'true';
}
if (isset($ArResult['BoPermission']) && $ArResult['BoPermission'] == 'true') {
  if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
    $StFileName = preg_replace('/\./', '%2e', $ArResult['StFile'], substr_count($ArResult['StFile'], '.') - 1);
  } else {
    $StFileName = $ArResult['StFile'];
  }
  $StFileName = strtr($StFileName,' ','_');
  if (isset($ArResult['StLink']) && ! is_dir($ArResult['StLink'])) {
    $ItFileSize = filesize($ArResult['StLink']);
    $tmpFile = F1DeskUtils::toTMP($ArResult['StLink'],'path');

  } else {
    $ItFileSize = mb_strlen($ArResult['ByFile'],'latin1');
    $tmpFile = F1DeskUtils::toTMP($ArResult['ByFile'],'file');
  }

  #
  # Verificar este header pois o mesmo não exibe corretamente o tamanho do arquivo, quando disponiblizado para download
  #
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  header("Content-Type: application/download");

  header("Content-Disposition: attachment; filename=" . $StFileName . ";");

  header("Content-Transfer-Encoding: binary");
  header("Content-Length: " . $ItFileSize);

  rewind($tmpFile);
  fpassthru($tmpFile);
}
?>