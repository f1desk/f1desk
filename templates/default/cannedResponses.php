<?if ( getSessionProp('isSupporter')=="true" ):?>
<?$ArCannedResponses = TemplateHandler::getCannedResponses( getSessionProp('IDSupporter') );?>
	<div id="cannedBox" class="homeBox">
		<span class="homeBoxTitle"><?=CANNED_RESPONSES?></span>
		<span class="homeBoxTitle newCannedResponse" onclick="startCreatingElement('canned');"><img src="<?= TEMPLATEDIR ?>images/new_canned.png"> Criar</span>
		<span class="homeBoxTitle loadingRequest" id="cannedLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"> Carregando...</span>
		<div id="cannedBoxContent" class="homeBoxContent">
			<table class="tableTickets" id="cannedTable">
				<thead>
					<th><?=ALIAS?></th>
					<th><?=TITLE?></th>
					<th><?=ACTIONS?></th>
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
								<input type="hidden" id="StCannedAlias<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value="<?=$ArCannedResponsesSettings['StAlias']?>">
							</td>
							<td>
								<?=$ArCannedResponsesSettings['StTitle']?>
								<input type="hidden" id="StCannedTitle<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value="<?=$ArCannedResponsesSettings['StTitle']?>">
							</td>
							<td>
								<input type="hidden" id="TxCannedResponse<?=$ArCannedResponsesSettings['IDCannedResponse']?>" value="<?=($ArCannedResponsesSettings['TxMessage'])?>">
								<img src="<?= TEMPLATEDIR ?>images/button_edit.png" alt="Editar" class="cannedAction" onclick="startEditElement('canned', <?=$ArCannedResponsesSettings['IDCannedResponse']?>);">
								<img src="<?= TEMPLATEDIR ?>images/button_cancel.png" alt="Remover" class="cannedAction" onclick="removeCannedResponse(<?=$ArCannedResponsesSettings['IDCannedResponse']?>)">
								<img src="<?= TEMPLATEDIR ?>images/visualizar.png" alt="Visualizar" class="cannedAction" onclick="previewInFlow('asd');">
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
						<?=ALIAS?>:	<br />
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