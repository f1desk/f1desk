<?
  require_once(dirname(__FILE__) . '/../../homeData.php');
  handleLanguage(__FILE__);
?>
<h3>
  <?=USER_DATA?>
  <div id="dataLoading" class="loading hidden">
    <img src="<?=TEMPLATEDIR?>images/loading.gif" />Carregando ...
  </div>
</h3>
<table id="dataTable" class="tableTickets">
	<tbody>
	  <th><?=NAME?>:</th>
		<tr>
			<td id='StDataNameTD'>
			 <pre><?=$ArUser['StName']?></pre>
			 <input type='hidden' id='StDataName' value='<?=f1desk_escape_string($ArUser['StName'],false,true)?>'>
		  </td>
		</tr>
		
		<th><?=EMAIL?>:</th>
		<tr>
			<td id='StDataEmailTD'>
			 <pre><?=$ArUser['StEmail']?></pre>
			 <input type='hidden' id='StDataEmail' value='<?=$ArUser['StEmail']?>'>
			</td>
		</tr>
		
		<th><?=NOTIFY?>:</th>
		<tr>
			<td id='StDataNotifyTD'>
			 <pre><?=($ArUser['BoNotify'])?DO_NOTIFY:DONT_NOTIFY?></pre>
			 <input type='hidden' id='StDataNotify' value='<?=$ArUser['BoNotify']?>'>
			</td>
		</tr>
		
		<th><?=HEADER?>:</th>
		<tr>
			<td id='TxDataHeaderTD'>
			 <pre><?=($ArUser['TxHeader'])?$ArUser['TxHeader']:'<i>'. EMPTY_TEXT .'</i>'?></pre>
			 <input type='hidden' id='TxDataHeader' value='<?=f1desk_escape_string($ArUser['TxHeader'],false,true)?>'>
			</td>
		</tr>
		
		<th><?=SIGN?>:</th>
		<tr>
			<td id='TxDataSignTD'>
			 <pre><?=($ArUser['TxSign'])?$ArUser['TxSign']:'<i>'. EMPTY_TEXT .'</i>'?></pre>
			 <input type='hidden' id='TxDataSign' value='<?=f1desk_escape_string($ArUser['TxSign'],false,true)?>'>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" id="dataEditAction" value="start">
<button id="dataButton" class='button' onclick='Home.editData();'>
	<img src='<?= TEMPLATEDIR ?>images/button_edit.png'>
	<span><?=EDIT_AREA?></span>
</button>
