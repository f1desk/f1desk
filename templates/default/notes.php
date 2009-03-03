<?if ( getSessionProp('isSupporter')=="true" ):?>
<?$ArNotes = TemplateHandler::listNotes( getSessionProp('IDSupporter') );?>
	<div id="noteBox" class="homeBox">
		<span class="homeBoxTitle"><?=NOTES?></span>
		<span class="homeBoxTitle newCannedResponse" onclick="startCreatingElement('note')"><img src="<?= TEMPLATEDIR ?>images/new_canned.png"> Criar</span>
		<span class="homeBoxTitle loadingRequest" id="noteLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"> Carregando...</span>
		<div id="noteBoxContent" class="homeBoxContent">
			<table class="tableTickets" id="noteTable">
				<thead>
					<th><?=TITLE?></th>
					<th><?=ACTIONS?></th>
				</thead>
				<tbody>
				<?if (count( $ArNotes ) == 0):?>
					<tr id="noNote">
						<td colspan="3" align="center"><?=NO_NOTES?></td>
					</tr>
				<?else:?>
					<?foreach ($ArNotes as $ArNoteSettings):?>
						<tr id="noteTR<?=$ArNoteSettings['IDNote']?>">
							<td>
								<?=$ArNoteSettings['StTitle']?>
								<input type="hidden" id="StNoteTitle<?=$ArNoteSettings['IDNote']?>" value="<?=$ArNoteSettings['StTitle']?>">
							</td>
							<td>
								<input type="hidden" id="TxNote<?=$ArNoteSettings['IDNote']?>" value="<?=$ArNoteSettings['TxNote']?>">
								<img src="<?= TEMPLATEDIR ?>images/button_edit.png" alt="Editar" class="cannedAction" onclick="startEditElement('note', <?=$ArNoteSettings['IDNote']?>);">
								<img src="<?= TEMPLATEDIR ?>images/button_cancel.png" alt="Remover" class="cannedAction" onclick="removeNote(<?=$ArNoteSettings['IDNote']?>)">
								<img src="<?= TEMPLATEDIR ?>images/visualizar.png" alt="Visualizar" class="cannedAction">
							</td>
						</tr>
					<?endforeach;?>
				<?endif;?>
				</tbody>
			</table>
			<div id="noteBoxEditArea" class="editArea">
				<div class="editAreaTitle">
					<img id="noteArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif" onclick="toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')">
					<span><?=EDIT_AREA?></span>
				</div>
				<div id="noteBoxEditAreaContent" class="editAreaContent" style="display: none">
					<form onsubmit="return false;" id="noteForm">
						<?=TITLE?>: <br />
							<input type="text" name="StTitle" class="inputCombo"> <br />
						<?=RESPONSE?>: <br />
							<textarea name="TxNote" class="answerArea"></textarea> <br>
							<input type="hidden" name="IDNote">
						<input type="button" value="Editar" id="noteFormButton" class="button" onclick="submitForm('note', this.value);">
						<button class="button" onclick="toogleArrow( 'noteArrow', 'noteBoxEditAreaContent', 'hide')">Cancelar</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?endif;?>