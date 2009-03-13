<?php
require_once('main.php');
$ArSupporters = F1DeskUtils::getAllSupporters();
if ($ArSupporters[0]['IDSupporter'] == 0) {
  array_shift($ArSupporters);
}
require_once(TEMPLATEDIR . 'addSupporters.php');
?>