<?php
/*default language*/
handleLanguage(__FILE__);
require_once('header.php');
?>
<div id="homeTemplate">

	<!-- First Box: User Datas -->
	<?
	if ( getSessionProp('isSupporter')=="false" ) 
		$ArUser = F1DeskUtils::getUserData( getSessionProp('IDClient'), 1);
	else 
		$ArUser = F1DeskUtils::getUserData( getSessionProp('IDSupporter'), 0);
	?>
	<div id="dataBox" class="homeBox">
		<span class="homeBoxTitle"><?=USER_DATA?></span>
		<div id="dataBoxContent" class="homeBoxContent">
			<table>
				<thead></thead>
				<tbody>
					<tr>
						<td class="TicketNumber"><?=NAME?>:</td>
						<td><?=$ArUser['StName']?></td>
					</tr>
					<tr>
						<td class="TicketNumber"><?=EMAIL?>:</td>
						<td><?=$ArUser['StEmail']?></td>
					</tr>
					<tr>
						<td class="TicketNumber"><?=HEADER?>:</td>
						<td><pre><?=(isset($ArUser['TxHeader']))?$ArUser['TxHeader']:'--'?></pre></td>
					</tr>
					<tr>
						<td class="TicketNumber"><?=SIGN?>:</td>
						<td><pre><?=(isset($ArUser['TxSign']))?$ArUser['TxSign']:'--'?></pre></td>
					</tr>
				</tbody>
			</table>
			<div id="dataBoxEditArea" class="editArea">
				<div class="editAreaTitle">
					<img id="dataArrow" src="<?= TEMPLATEDIR ?>images/arrow_show.gif">
					<span><?=EDIT_AREA?></span>
				</div>
				<div id="dataBoxEditAreaContent" class="editAreaContent"></div>
			</div>
		</div>
	</div>
	<!-- First Box End -->
	
	<!-- Second Box: Supporter Canned Responses -->
	<?require_once("cannedResponses.php");?>
	<!-- Second Box End -->
	
	<!--<div class="homeBox">
		<span id="homeBoxTitle">Teste do Mario3</span>
		<div id="homeBoxContent">
			asdasd asdasd asd as d asd as da sd as d asd as d
			<div id="editArea">
				<div id="editAreaTitle">
					<img src="<?= TEMPLATEDIR ?>images/arrow_show.gif">
					<span><?=EDIT_AREA?></span>
				</div>
				<div id="editAreaContent"></div>
			</div>
		</div>
	</div>-->
	
</div><!-- HOME TEMPLATE -->
<?php
require_once('footer.php');
?>