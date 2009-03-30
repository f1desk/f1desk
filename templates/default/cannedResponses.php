<?php
require_once('homeData.php');
handleLanguage(__FILE__);
if (TemplateHandler::IsSupporter()):
?>
  <div id="cannedBox" class="homeBox">
    <span class="homeBoxTitle" onclick="toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')"><?=CANNED_RESPONSES?></span>
    <span class="homeBoxTitle newElement" onclick="startCreatingElement('canned');"><img src="<?= TEMPLATEDIR ?>images/new_canned.png"> Criar</span>
    <span class="homeBoxTitle loadingRequest" id="cannedLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"><?=LOAD?></span>
    <div id="cannedBoxContent" class="homeBoxContent">
      <table class="tableTickets" id="cannedTable">
        <thead>
          <th><?=TITLE?></th>
          <th width="20%"><?=ACTIONS?></th>
        </thead>
        <tbody>
        <?=TemplateHandler::showCannedAnswers($ArCannedResponses);?>
        </tbody>
      </table>
      <div id="cannedBoxEditArea" class="editArea">
        <div class="editAreaTitle" onclick="toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')">
          <img id="cannedArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif" >
          <span><?=EDIT_AREA?></span>
        </div>
        <div id="cannedBoxEditAreaContent" class="editAreaContent" style="display: none">
          <form onsubmit="return false;" id="cannedForm">
            <?=TITLE?>: <br />
              <input type="text" name="StTitle" class="inputCombo"> <br />
            <?=RESPONSE?>: <br />
              <textarea name="TxCannedResponse" class="answerArea"></textarea> <br>
              <input type="hidden" name="IDCanned">
            <input type="button" value="Editar" id="cannedFormButton" class="button" onclick="submitForm('canned', this.value);">
            <button class="button" onclick="toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')">Cancelar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?endif;?>