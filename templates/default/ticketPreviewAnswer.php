<?require_once( dirname(__FILE__) . '/../../ticketData.php' );?>

<!--[ERROR/OK BOX]-->
  <?= ErrorHandler::getNotice(); ?>
<!--[ERROR/OK BOX]-->

<div id="previewAnswer">
  <h3>Sua Resposta</h3>
  <div class="message<?=($_POST['StMessageType'] == 'INTERNAL')?'Internal':''?>">
    <?$TxMessagePreview = (isset($TxMessagePreview))?nl2br($TxMessagePreview):' -- '?>
    <?=$TxMessagePreview?>
  </div>
</div>