<? handleLanguage(__FILE__); ?>
  <!--[ERROR/OK BOX]-->
  <? if(isset($returnMessage) && isset($returnType)): ?>
    <div class="boxmsg <?=$returnType?>">
      <?=$returnMessage ?>
    </div>
  <? endif; ?>
  <!--[ERROR/OK BOX]-->
	<span class='homeBoxTitle' onclick="baseActions.toogleArrow('dataArrow', 'dataBoxEditAreaContent', 'hide')"><?=USER_DATA?></span>
	<span class='homeBoxTitle newElement' onclick='Home.startDataEdit();'><img src='<?= TEMPLATEDIR ?>images/button_edit.png'> Editar</span>
	<span class='homeBoxTitle loadingRequest' id='dataLoading'><img src='<?= TEMPLATEDIR ?>images/loading.gif'> Carregando...</span>
	<div id='dataBoxContent' class='homeBoxContent'>
		<table>
			<thead></thead>
			<tbody>
				<tr>
					<td class='TicketNumber'><?=NAME?>:</td>
					<td id='StDataNameTD'>
					 <pre><?=$ArUser['StName']?></pre>
					 <input type='hidden' id='StDataName' value='<?=f1desk_escape_string($ArUser['StName'],false,true)?>'>
				  </td>
				</tr>
				<tr>
					<td class='TicketNumber'><?=EMAIL?>:</td>
					<td id='StDataEmailTD'>
					 <pre><?=$ArUser['StEmail']?></pre>
					 <input type='hidden' id='StDataEmail' value='<?=$ArUser['StEmail']?>'>
					</td>
				</tr>
				<tr>
				  <td class='TicketNumber'><?=PASS?></td>
				  <td>
				    <pre> ******** </pre>
				  </td>
				</tr>
				<tr>
					<td class='TicketNumber'><?=NOTIFY?>:</td>
					<td id='StDataNotifyTD'>
					 <pre><?=($ArUser['BoNotify'])?DO_NOTIFY:DONT_NOTIFY?></pre>
					 <input type='hidden' id='StDataNotify' value='<?=$ArUser['BoNotify']?>'>
					</td>
				</tr>
				<tr>
					<td class='TicketNumber'><?=HEADER?>:</td>
					<td id='TxDataHeaderTD' style='border:solid 1px #ccc;'>
					 <pre><?=($ArUser['TxHeader'])?$ArUser['TxHeader']:'<i>'. EMPTY_TEXT .'</i>'?></pre>
					 <input type='hidden' id='TxDataHeader' value='<?=f1desk_escape_string($ArUser['TxHeader'],false,true)?>'>
					</td>
				</tr>
				<tr>
					<td class='TicketNumber'><?=SIGN?>:</td>
					<td id='TxDataSignTD' style='border:solid 1px #ccc;'>
					 <pre><?=($ArUser['TxSign'])?$ArUser['TxSign']:'<i>'. EMPTY_TEXT .'</i>'?></pre>
					 <input type='hidden' id='TxDataSign' value='<?=f1desk_escape_string($ArUser['TxSign'],false,true)?>'>
					</td>
				</tr>
			</tbody>
		</table>
		<div id='dataBoxEditArea' class='editArea'>
			<div class='editAreaTitle'  onclick='Home.startDataEdit();'>
				<img id='dataArrow' src='<?= TEMPLATEDIR ?>images/arrow_show.gif'>
				<span><?=EDIT_AREA?></span>
			</div>
			<div id='dataBoxEditAreaContent' class='editAreaContent' style='display: none'>
				<form name='dataForm' id='dataForm' onsubmit='return false;'>
					<p><?=NAME?>:</p>
					<input type='text' class='inputCombo' name='StDataName'>	<br />
					<p><?=EMAIL?>:</p>
					<input type='text' class='inputCombo' name='StDataEmail'>	<br />
					<p><?=PASS?></p>
					<input type='password' class='inputCombo' name='StDataPassword'>	<br />
          <p><?=NOTIFY?>:</p>
          <input type='radio' class='inputCombo' name='StDataNotify' value='<?=DONT_NOTIFY?>'><?=DONT_NOTIFY?>
          <input type='radio' class='inputCombo' name='StDataNotify' value='<?=DO_NOTIFY?>'><?=DO_NOTIFY?><br />
          <p><?=HEADER?>:</p>
					<textarea class='answerArea' name='TxDataHeader' ></textarea>
					<p><?=SIGN?>:</p>
					<textarea class='answerArea' name='TxDataSign' ></textarea>
					<button class='button' onclick='Home.updateInformations()'>Salvar</button>
					<button class='button' onclick="baseActions.toogleArrow('dataArrow', 'dataBoxEditAreaContent', 'hide')">Cancelar</button>
				</form>
			</div>
		</div>
	</div>