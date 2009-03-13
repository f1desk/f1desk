<?if (TemplateHandler::IsSupporter()) :?>
<?$ArCannedResponses = TemplateHandler::getCannedResponses( getSessionProp('IDSupporter') );?>
  <div id="cannedBox" class="homeBox">
    <span class="homeBoxTitle" onclick="toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')"><?=CANNED_RESPONSES?></span>
    <span class="homeBoxTitle newElement" onclick="startCreatingElement('canned');"><img src="<?= TEMPLATEDIR ?>images/new_canned.png"> Criar</span>
    <span class="homeBoxTitle loadingRequest" id="cannedLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"> Carregando...</span>
    <div id="cannedBoxContent" class="homeBoxContent">
      <table class="tableTickets" id="cannedTable">
        <thead>
          <th><?=ALIAS?></th>
          <th><?=TITLE?></th>
          <th width="20%"><?=ACTIONS?></th>
        </thead>
        <tbody>
        <?if ($ArCannedResponses[0]['IDCannedResponse'] == ''):?>
          <tr id="noCanned">
            <td colspan="3" align="center"><?=NO_CANNED?></td>
          </tr>
        <?else:?>
          <?foreach ($ArCannedResponses as $ArCannedResponsesSettings):?>
            <tr id="cannedTR<?=$ArCannedResponsesSettings['IDCannedResponse']?>">
              <td class="TicketNumber">
                <?=$ArCannedResponsesSettings['StAlias']?>
                <input type="hidden" id="StCannedAlias<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value=<?=f1desk_escape_string($ArCannedResponsesSettings['StAlias'],false,true)?> >
              </td>
              <td>
                <?=$ArCannedResponsesSettings['StTitle']?>
                <input type="hidden" id="StCannedTitle<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value=<?=f1desk_escape_string($ArCannedResponsesSettings['StTitle'],false,true)?> >
              </td>
              <td>
                <input type="hidden" id="TxCannedResponse<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value='<?=f1desk_escape_string($ArCannedResponsesSettings['TxMessage'],false,true)?>'>
                <img src="<?= TEMPLATEDIR ?>images/button_edit.png" alt="Editar" title="Editar" class="cannedAction" onclick="startEditElement('canned', <?=$ArCannedResponsesSettings['IDCannedResponse']?>);">
                <img src="<?= TEMPLATEDIR ?>images/button_cancel.png" alt="Remover" title="Remover" class="cannedAction" onclick="removeCannedResponse(<?=$ArCannedResponsesSettings['IDCannedResponse']?>)">
                <img src="<?= TEMPLATEDIR ?>images/visualizar.png" title="Visualizar" id="previemCanned<?=$ArCannedResponsesSettings['IDCannedResponse']?>" alt="Visualizar" class="cannedAction" onclick='previewInFlow.CannedResponse("<?=f1desk_escape_string($ArCannedResponsesSettings['StAlias'],false,true)?>", "<?=f1desk_escape_string($ArCannedResponsesSettings['StTitle'],false,true)?>", "<?=f1desk_escape_string($ArCannedResponsesSettings['TxMessage'], true,true)?>");'>
              </td>
            </tr>
          <?endforeach;?>
        <?endif;?>
        </tbody>
      </table>
      <div id="cannedBoxEditArea" class="editArea">
        <div class="editAreaTitle" onclick="toogleArrow( 'cannedArrow', 'cannedBoxEditAreaContent', 'hide')">
          <img id="cannedArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif" >
          <span><?=EDIT_AREA?></span>
        </div>
        <div id="cannedBoxEditAreaContent" class="editAreaContent" style="display: none">
          <form onsubmit="return false;" id="cannedForm">
            <?=ALIAS?>:  <br />
              <input type="text" name="StAlias" class="inputCombo"> <br />
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