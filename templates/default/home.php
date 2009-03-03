<?php
/*default language*/
handleLanguage(__FILE__);
require_once('header.php');
?>
<div id="homeTemplate">

	<!-- First Box: User Datas -->
	<?
	if ( getSessionProp('isSupporter') == "true" ){
		$ArUser = TemplateHandler::getUserData( getSessionProp('IDSupporter'), 0);
	} else {
		$ArUser = TemplateHandler::getUserData( getSessionProp('IDClient'), 1);
	}
	
	?>
	<div id="dataBox" class="homeBox">
		<span class="homeBoxTitle"><?=USER_DATA?></span>
		<div id="dataBoxContent" class="homeBoxContent">
			<table>
				<thead></thead>
				<tbody>
					<tr>
						<td class="TicketNumber"><?=NAME?>:</td>
						<td id="StDataName"><?=$ArUser['StName']?></td>
					</tr>
					<tr>
						<td class="TicketNumber"><?=EMAIL?>:</td>
						<td id="StDataEmail"><?=$ArUser['StEmail']?></td>
					</tr>
					<tr>
						<td class="TicketNumber"><?=HEADER?>:</td>
						<td id="TxDataHeader" style="border:solid 1px #ccc;"><pre><?=($ArUser['TxHeader'])?$ArUser['TxHeader']:'--'?></pre></td>
					</tr>
					<tr>
						<td class="TicketNumber"><?=SIGN?>:</td>
						<td id="TxDataSign"  style="border:solid 1px #ccc;"><pre><?=($ArUser['TxSign'])?$ArUser['TxSign']:'--'?></pre></td>
					</tr>
				</tbody>
			</table>
			<div id="dataBoxEditArea" class="editArea">
				<div class="editAreaTitle">
					<img id="dataArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif" onclick="toogleArrow('dataArrow', 'dataBoxEditAreaContent'); startDataEdit();">
					<span><?=EDIT_AREA?></span>
				</div>
				<div id="dataBoxEditAreaContent" class="editAreaContent" style="display: none">
					<form name="dataForm" id="dataForm" onsubmit="return false;">
						<?=NAME?>:	<br />
						<input type="text" class="inputCombo" name="StDataName">	<br />
						<?=EMAIL?>:	<br />
						<input type="text" class="inputCombo" name="StDataEmail">	<br />
						<?=HEADER?>:	<br />
						<textarea class="answerArea" name="TxDataHeader" ></textarea>
						<?=SIGN?>:	<br />
						<textarea class="answerArea" name="TxDataSign" ></textarea>
						<button class="button" onclick="updateInformations()">Salvar</button>
						<button class="button" onclick="toogleArrow('dataArrow', 'dataBoxEditAreaContent', 'hide')">Cancelar</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- First Box End -->

	<!-- Second Box: Supporter Canned Responses -->
	<?require_once("cannedResponses.php");?>
	<!-- Second Box End -->

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

</div><!-- HOME TEMPLATE -->
<?php
require_once('footer.php');
?>