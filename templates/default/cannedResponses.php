<?if (TemplateHandler::IsSupporter()) :?>
<?$ArCannedResponses = TemplateHandler::getCannedResponses( getSessionProp('IDSupporter') );?>
  <div id="cannedBox" class="homeBox">
    <span class="homeBoxTitle" onclick="baseActions.toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')"><?=CANNED_RESPONSES?></span>
    <span class="homeBoxTitle newElement" onclick="HOME.startCreatingElement('canned');"><img src="<?= TEMPLATEDIR ?>images/new_canned.png"> Criar</span>
    <span class="homeBoxTitle loadingRequest" id="cannedLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"> Carregando...</span>
    <div id="cannedBoxContent" class="homeBoxContent">
      <table class="tableTickets" id="cannedTable">
        <thead>
          <th><?=TITLE?></th>
          <th width="20%"><?=ACTIONS?></th>
        </thead>
        <tbody>
        <?if ($ArCannedResponses[0]['IDCannedResponse'] == ''):?>
          <tr id="noCanned">
            <td colspan="2" align="center"><?=NO_CANNED?></td>
          </tr>
        <?else:?>
          <?foreach ($ArCannedResponses as $ArCannedResponsesSettings):?>
            <tr id="cannedTR<?=$ArCannedResponsesSettings['IDCannedResponse']?>">
              <td class="TicketNumber">
                <?=$ArCannedResponsesSettings['StTitle']?>
                <input type="hidden" id="StCannedTitle<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value=<?=f1desk_escape_string($ArCannedResponsesSettings['StTitle'],false,true)?> >
              </td>
              <td>
                <input type="hidden" id="TxCannedResponse<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value='<?=f1desk_escape_string($ArCannedResponsesSettings['TxMessage'],false,true)?>'>
                <img src="<?= TEMPLATEDIR ?>images/button_edit.png" alt="Editar" title="Editar" class="cannedAction" onclick="HOME.startEditElement('canned', <?=$ArCannedResponsesSettings['IDCannedResponse']?>);">
                <img src="<?= TEMPLATEDIR ?>images/button_cancel.png" alt="Remover" title="Remover" class="cannedAction" onclick="HOME.removeCannedResponse(<?=$ArCannedResponsesSettings['IDCannedResponse']?>)">
                <img src="<?= TEMPLATEDIR ?>images/visualizar.png" title="Visualizar" id="previemCanned<?=$ArCannedResponsesSettings['IDCannedResponse']?>" alt="Visualizar" class="cannedAction" onclick='flowWindow.previewCannedResponse("<?=f1desk_escape_string($ArCannedResponsesSettings['StTitle'],false,true)?>", "<?=f1desk_escape_string($ArCannedResponsesSettings['TxMessage'], true,true)?>");'>
              </td>
            </tr>
          <?endforeach;?>
        <?endif;?>
        </tbody>
      </table>
      <div id="cannedBoxEditArea" class="editArea">
        <div class="editAreaTitle" onclick="baseActions.toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')">
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
            <input type="button" value="Editar" id="cannedFormButton" class="button" onclick="HOME.submitForm('canned', this.value);">
            <button class="button" onclick="baseActions.toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')">Cancelar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?endif;?>