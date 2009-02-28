<div id="answerCaption">
	<img alt="Ticket" id='arrowAnswer' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "answerContent")' class="menuArrow"/>
	<span>Responder</span>
</div>

<div id="answerContent" >
  <form method="POST" id="formAnswer" target="ajaxSubmit" action="answerTicket.php" enctype="multipart/form-data">
    <div id='messageType' class='Right'>
  	  <select name='StMessageType' id='StMessageType' class='inputCombo'>
  				<option value="NORMAL"><?=MSGTYPE_NORMAL?></option>
  				<option value="INTERNAL"><?=MSGTYPE_INTERNAL?></option>
  				<option value="SYSTEM"><?=MSGTYPE_SYSTEM?></option>
  				<option value="SATISFACTION"><?=MSGTYPE_SATISFACTION?></option>
  		</select>
    </div>

  	<textarea id='TxMessage' name='TxMessage' cols='65' rows='33' class='answerArea'></textarea>

    <div id='displayCommands'>
      <div id='answerOptions'>

    		<div class='Right' id='answerAttach'>
    		  <label for='Attachment'> Anexo : </label>
    			<input id='Attachment' name='Attachment' class='inputFile' type="file" value="Anexo" />
    			<iframe id='ajaxSubmit' name='ajaxSubmit' src='answerTicket.php'></iframe>
    		</div>
  		  <div>
  		    <input type='hidden' name='IDDepartment' id='IDDepartment' value='<?= $IDDepartment ?>' />
  		    <input type='hidden' name='IDTicket' id='IDTicket' value='<?= $IDTicket ?>' />
  		    <select class='inputCombo' id='cannedAnswers'>
          <? if ($ArResponses[0]['IDCannedResponse'] != ''): ?>
              <? foreach ($ArResponses as $Response): ?>
                <option value="<?=$Response['IDCannedResponse'];?>"><?=$Response['StTitle']?></option>
              <?endforeach; ?>
          <? else: ?>
               <option value='null'><?=NO_ANSWER?></option>
          <? endif; ?>
  			  </select>
  			 <button class='button'>Incluir</button>
  		</div>
  	</div>

  	<div>
  			<input type='submit' class='button' value='Responder' name='Responder'>
  	</div>
  </div>
  </form>
</div>