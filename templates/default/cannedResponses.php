<?
  require_once(dirname(__FILE__) . '/../../homeData.php');
  handleLanguage(__FILE__);
?>
<h3>
  <?=CANNED_RESPONSES?>
  <div id="cannedResponseLoading" class="loading hidden">
    <img src="<?=TEMPLATEDIR?>images/loading.gif" />Carregando ...
  </div>
</h3>
<table class="tableTickets homeTable" id="cannedTable">
  <thead>
    <th><?=TITLE?></th>
    <th width="20%"><?=ACTIONS?></th>
  </thead>
  <tbody>
    <? if ( $ArCannedResponses[0]['IDCannedResponse'] != ""): ?>
      <? foreach ($ArCannedResponses as $Alt => $ArAnswer): ?>
        <tr id="cannedTitleTR<?=$ArAnswer['IDCannedResponse']?>" class="<?=(($Alt%2)==0)?'Alt':''?>">
          <td class="TicketNumber">
            <?=$ArAnswer['StTitle']?>
          </td>
          <td>
            <div id="cannedActionEdit<?=$ArAnswer['IDCannedResponse']?>" class="">
              <img src="<?=TEMPLATEDIR?>images/button_edit.png" onclick="Home.startEditElement('canned','<?=$ArAnswer['IDCannedResponse']?>')">
              <img src="<?=TEMPLATEDIR?>images/button_cancel.png" onclick="Home.removeCannedResponse('<?=$ArAnswer['IDCannedResponse']?>')">
              <img src="<?=TEMPLATEDIR?>images/visualizar.png" onclick='flowWindow.previewCannedResponse("<?=f1desk_escape_string($ArAnswer['StTitle'],false,true)?>", "<?=f1desk_escape_string($ArAnswer['TxMessage'], true,true)?>");'>
            </div>
            <div id="cannedActionApply<?=$ArAnswer['IDCannedResponse']?>" class="hiddenTR">
              <img src="<?=TEMPLATEDIR?>images/unignore.png" onclick="Home.elementEditSubmit('canned','<?=$ArAnswer['IDCannedResponse']?>')">
              <img src="<?=TEMPLATEDIR?>images/ignore.png" onclick="Home.stopEditElement('canned','<?=$ArAnswer['IDCannedResponse']?>')">
            </div>
          </td>
        </tr>
        <tr id="cannedAnswerTR<?=$ArAnswer['IDCannedResponse']?>" class="hiddenTR">
          <td><textarea id="cannedAnswer<?=$ArAnswer['IDCannedResponse']?>" class="answerArea"><?=$ArAnswer['TxMessage']?></textarea></td>
        </tr>
      <? endforeach; ?>
    <? else: ?>
      <tr>
        <td colspan="2" align="center"><?=NO_CANNED?></td>
      </tr>
    <? endif; ?>
    <tr id="cannedInsertTitleTR" class="hiddenTR">
      <td>
        <input type="text" id="cannedInsertTitle" class="inputCombo">
      </td>
      <td>
        <div>
          <img src="<?=TEMPLATEDIR?>images/unignore.png" onclick="Home.elementCreateSubmit('canned')">
          <img src="<?=TEMPLATEDIR?>images/ignore.png" onclick="Home.stopCreateElement('canned')">
        </div>
      </td>
    </tr>
    <tr id="cannedInsertAnswerTR" class="hiddenTR">
      <td><textarea id="cannedInsertAnswer" class="answerArea"></textarea></td>
    </tr>
  </tbody>
</table>

<button id="cannedInsertButton" onclick="Home.startCreateElement('canned');" class="button">
	<img src="<?=TEMPLATEDIR?>images/new_canned.png"/>
	<span><?=CREATE_CANNED_RESPONSE?></span>
</button>