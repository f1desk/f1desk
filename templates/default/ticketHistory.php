<div id="historyCaption">
	<img alt="Ticket"  id='arrowHistory' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif"  onclick='toogleArrow( this.id, "historyContent")' class="menuArrow"/>
	<span>Hist&oacute;rico</span>
</div>

<div id="historyContent" >

  <? foreach ($ArMessages as $ArMessage) : ?>
    <? $DtSended = F1DeskUtils::formatDate('datetime_format',$ArMessage['DtSended']); ?>
    <div class='<?= $ArMessage['StClass'] ?>'>
      <?= MSG_HEAD1 . $DtSended . MSG_HEAD2 . $ArMessage['SentBy'] . MSG_HEAD3 . $ArMessage['TxMessage'] ?>
    </div>
  <? endforeach ?>

</div>