<?
  require_once(dirname(__FILE__) . '/../../homeData.php');
  handleLanguage(__FILE__);
?>
<h3>
  <?=NOTES?>
  <div id="notesResponseLoading" class="loading hidden">
    <img src="<?=TEMPLATEDIR?>images/loading.gif" />Carregando ...
  </div>
</h3>
<table class="tableTickets homeTable" id="notesTable">
  <thead>
    <th><?=NOTE_TITLE?></th>
    <th width="20%"><?=NOTE_ACTIONS?></th>
  </thead>
  <tbody>
    <? if ( count($ArNotes) != 0): ?>
      <? foreach ($ArNotes as $Alt => $ArAnswer): ?>
        <tr id="notesTitleTR<?=$ArAnswer['IDNote']?>" class="<?=(($Alt%2)==0)?'Alt':''?>">
          <td class="TicketNumber">
            <?=$ArAnswer['StTitle']?>
          </td>
          <td>
            <div id="notesActionEdit<?=$ArAnswer['IDNote']?>" class="">
              <img src="<?=TEMPLATEDIR?>images/button_edit.png" onclick="Home.startEditElement('notes','<?=$ArAnswer['IDNote']?>')">
              <img src="<?=TEMPLATEDIR?>images/button_cancel.png" onclick="Home.removeNote('<?=$ArAnswer['IDNote']?>')">
              <img src="<?=TEMPLATEDIR?>images/visualizar.png" onclick='flowWindow.previewNote("<?=f1desk_escape_string($ArAnswer['StTitle'],false,true)?>", "<?=f1desk_escape_string($ArAnswer['TxNote'], true,true)?>");'>
            </div>
            <div id="notesActionApply<?=$ArAnswer['IDNote']?>" class="hiddenTR">
              <img src="<?=TEMPLATEDIR?>images/unignore.png" onclick="Home.elementEditSubmit('notes','<?=$ArAnswer['IDNote']?>')">
              <img src="<?=TEMPLATEDIR?>images/ignore.png" onclick="Home.stopEditElement('notes','<?=$ArAnswer['IDNote']?>')">
            </div>
          </td>
        </tr>
        <tr id="notesAnswerTR<?=$ArAnswer['IDNote']?>" class="hiddenTR">
          <td><textarea id="notesAnswer<?=$ArAnswer['IDNote']?>" class="answerArea"><?=$ArAnswer['TxNote']?></textarea></td>
        </tr>
      <? endforeach; ?>
    <? else: ?>
      <tr>
        <td colspan="2" align="center"><?=NO_NOTES?></td>
      </tr>
    <? endif; ?>
    <tr id="notesInsertTitleTR" class="hiddenTR">
      <td>
        <input type="text" id="notesInsertTitle" class="inputCombo">
      </td>
      <td>
        <div>
          <img src="<?=TEMPLATEDIR?>images/unignore.png" onclick="Home.elementCreateSubmit('notes')">
          <img src="<?=TEMPLATEDIR?>images/ignore.png" onclick="Home.stopCreateElement('notes')">
        </div>
      </td>
    </tr>
    <tr id="notesInsertAnswerTR" class="hiddenTR">
      <td><textarea id="notesInsertAnswer" class="answerArea"></textarea></td>
    </tr>
  </tbody>
</table>

<button id="notesInsertButton" onclick="Home.startCreateElement('notes');" class="button">
	<img src="<?=TEMPLATEDIR?>images/new_canned.png"/>
	<span><?=CREATE_NOTE?></span>
</button>