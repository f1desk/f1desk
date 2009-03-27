<?if ( getSessionProp('isSupporter')=="true" ):?>
<?$ArNotes = TemplateHandler::listNotes( getSessionProp('IDSupporter') );?>
  <div id="noteBox" class="homeBox">
    <span class="homeBoxTitle" onclick="baseActions.toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')"><?=NOTES?></span>
    <span class="homeBoxTitle newElement" onclick="HOME.startCreatingElement('note')"><img src="<?= TEMPLATEDIR ?>images/new_canned.png"> Criar</span>
    <span class="homeBoxTitle loadingRequest" id="noteLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"> Carregando...</span>
    <div id="noteBoxContent" class="homeBoxContent">
      <table class="tableTickets" id="noteTable">
        <thead>
          <th><?=TITLE?></th>
          <th width="20%"><?=ACTIONS?></th>
        </thead>
        <tbody>
        <?if (count( $ArNotes ) == 0):?>
          <tr id="noNote">
            <td colspan="3" align="center"><?=NO_NOTES?></td>
          </tr>
        <?else:?>
          <?foreach ($ArNotes as $ArNoteSettings):?>
            <tr id="noteTR<?=$ArNoteSettings['IDNote']?>">
              <td class="TicketNumber">
                <?=$ArNoteSettings['StTitle']?>
                <input type="hidden" id="StNoteTitle<?=$ArNoteSettings['IDNote']?>" value=<?=f1desk_escape_string($ArNoteSettings['StTitle'],false,true)?> >
              </td>
              <td>
                <input type="hidden" id="TxNote<?=$ArNoteSettings['IDNote']?>" value='<?=f1desk_escape_string($ArNoteSettings['TxNote'],false,true)?>'>
                <img src="<?= TEMPLATEDIR ?>images/button_edit.png" alt="Editar" title="Editar" class="cannedAction" onclick="HOME.startEditElement('note', <?=$ArNoteSettings['IDNote']?>);">
                <img src="<?= TEMPLATEDIR ?>images/button_cancel.png" alt="Remover" title="Remover" class="cannedAction" onclick="HOME.removeNote(<?=$ArNoteSettings['IDNote']?>)">
                <img src="<?= TEMPLATEDIR ?>images/visualizar.png" alt="Visualizar" title="Visualizar" class="cannedAction" onclick='flowWindow.previewNote("<?=f1desk_escape_string($ArNoteSettings['StTitle'],false,true)?>", "<?=f1desk_escape_string($ArNoteSettings['TxNote'], true, true)?>");'>
              </td>
            </tr>
          <?endforeach;?>
        <?endif;?>
        </tbody>
      </table>
      <div id="noteBoxEditArea" class="editArea">
        <div class="editAreaTitle" onclick="baseActions.toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')">
          <img id="noteArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif">
          <span><?=EDIT_AREA?></span>
        </div>
        <div id="noteBoxEditAreaContent" class="editAreaContent" style="display: none">
          <form onsubmit="return false;" id="noteForm">
            <?=TITLE?>: <br />
              <input type="text" name="StTitle" class="inputCombo"> <br />
            <?=RESPONSE?>: <br />
              <textarea name="TxNote" class="answerArea"></textarea> <br>
              <input type="hidden" name="IDNote">
            <input type="button" value="Editar" id="noteFormButton" class="button" onclick="HOME.submitForm('note', this.value);">
            <button class="button" onclick="baseActions.toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')">Cancelar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?endif;?>