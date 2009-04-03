<? handleLanguage(__FILE__);?>
  <!--[ERROR/OK BOX]-->
  <? if(isset($returnMessage) && isset($returnType)): ?>
    <div class="boxmsg <?=$returnType?>">
      <?=$returnMessage ?>
    </div>
  <? endif; ?>
  <!--[ERROR/OK BOX]-->
    <span class="homeBoxTitle" onclick="baseActions.toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')"><?=NOTES?></span>
    <span class="homeBoxTitle newElement" onclick="Home.startCreatingElement('note')"><img src="<?= TEMPLATEDIR ?>images/new_canned.png"> Criar</span>
    <span class="homeBoxTitle loadingRequest" id="noteLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"> Carregando...</span>
    <div id="noteBoxContent" class="homeBoxContent">
      <table class="tableTickets" id="noteTable">
        <thead>
          <th><?=NOTE_TITLE?></th>
          <th width="20%"><?=NOTE_ACTIONS?></th>
        </thead>
        <tbody>
          <?=TemplateHandler::showNotes($ArNotes)?>
        </tbody>
      </table>
      <div id="noteBoxEditArea" class="editArea">
        <div class="editAreaTitle" onclick="baseActions.toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')">
          <img id="noteArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif">
          <span><?=EDIT_NOTES_AREA?></span>
        </div>
        <div id="noteBoxEditAreaContent" class="editAreaContent" style="display: none">
          <form onsubmit="return false;" id="noteForm">
            <?=NOTE_TITLE?>: <br />
              <input type="text" name="StTitle" class="inputCombo"> <br />
            <?=NOTE?>: <br />
              <textarea name="TxNote" class="answerArea"></textarea> <br>
              <input type="hidden" name="IDNote">
            <button id="noteFormButton" class="button" onclick="Home.submitForm('note', this.textContent);">Editar</button>
            <button class="button" onclick="baseActions.toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')">Cancelar</button>
          </form>
        </div>
      </div>
    </div>
  </div>