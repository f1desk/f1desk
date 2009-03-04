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
			<div class="editAreaTitle"  onclick="startDataEdit();">
				<img id="dataArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif">
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