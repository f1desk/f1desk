<!--[TICKET HEADER]-->
<div id='ticketHeader'>
  <div id="ticketTitle">
    <img id='reloadHeader' class='menuRefresh Right' onclick='refreshCall( <?= $IDTicket ?> )' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' />
  	<img alt="Ticket" id='arrowHeader' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "ticketContent")' class='menuArrow'/>
  	<span><?= $StTitle ?></span>
  </div>


  <div id="ticketContent">
  	<table class='tableTickets'>
      <thead>
        <tr>
          <th>ID</th>
          <th>Data</th>
          <th>Status</th>
          <th>Atendente</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class='TicketNumber'>#<?= $IDTicket ?></td>
          <td><?= $DtOpened ?></td>
          <td><?= $StSituation ?></td>
          <td>
          	<select id='StSupporter' onchange='setTicketOwner(<?= $IDTicket ?>, this.value)' class='inputCombo'>
          	  <? foreach ( $ArSupporters as $IDSupporter => $StSupporter ) : ?>
            	  <? if ($ArHeaders['IDSupporter'] != $IDSupporter) : ?>
            	  <option value=<?=$IDSupporter?>><?=$StSupporter?></option>
            		<? else : ?>
            		<option selected='selected' value=<?=$IDSupporter?>><?=$StSupporter?></option>
            		<? endif; ?>
          		<? endforeach; ?>
          	</select>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<!--[/TICKET HEADER]-->

<!--[TICKET HISTORY]-->
<div id='ticketHistory'>
  <div id="historyCaption">
  	<img alt="Ticket"  id='arrowHistory' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif"  onclick='toogleArrow( this.id, "historyContent")' class="menuArrow"/>
  	<span>Hist&oacute;rico</span>
  </div>

  <div id="historyContent" >

    <? foreach ($ArMessages as $ArMessage) : ?>
      <? $DtSended = F1DeskUtils::formatDate('datetime_format',$ArMessage['DtSended']); ?>
      <div class='<?= $ArMessage['StClass'] ?>'>
        <?= MSG_HEAD1 . $DtSended . MSG_HEAD2 . $ArMessage['SentBy'] . MSG_HEAD3 ?>
        <? if (array_key_exists($ArMessage['IDMessage'],$ArAttachments)): ?>
          <?  foreach ($ArAttachments[$ArMessage['IDMessage']] as $Attachment): ?>
              <p class='AttachLink'><a href='download.php?IDAttach=<?=$Attachment['IDAttachment']?>'><?=$Attachment['StFile']?></a></p>
          <?  endforeach; ?>
        <? endif;?>
        <?= $ArMessage['TxMessage'] ?>
      </div>
    <? endforeach ?>
    <?php ?>
  </div>
</div>
<!--[/TICKET HISTORY]-->

<!--[TICKET ANSWER]-->
<div id='ticketAnswer'>
  <div id="answerCaption">
  	<img alt="Ticket" id='arrowAnswer' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "answerContent")' class="menuArrow"/>
  	<span>Responder</span>
  </div>

  <div id="answerContent" >
    <form method="POST" id="formAnswer" target="ajaxSubmit" action="answerTicket.php" enctype="multipart/form-data">
      <div id='messageType' class='Right'>
    	  <select name='StMessageType' id='StMessageType' class='inputCombo'>
    				<option value="NORMAL"><?=MSGTYPE_NORMAL?></option>
    				<?php if (TemplateHandler::IsSupporter()): ?>
    				<option value="INTERNAL"><?=MSGTYPE_INTERNAL?></option>
    				<option value="SATISFACTION"><?=MSGTYPE_SATISFACTION?></option>
    				<?php endif; ?>
    		</select>
      </div>

    	<textarea id='TxMessage' name='TxMessage' cols='65' rows='33' class='answerArea'></textarea>

      <div id='displayCommands'>
        <div id='answerOptions'>

      		<div class='Right' id='answerAttach'>
      		  <label for='Attachment'> Anexo : </label>
      			<input id='Attachment' name='Attachment' class='inputFile' type="file" value="Anexo" />
      			<iframe id='ajaxSubmit' name='ajaxSubmit' src='answerTicket.php' class="Invisible"></iframe>
      		</div>
    		  <div>
    		    <input type='hidden' name='IDDepartment' id='IDDepartment' value='<?= $IDDepartment ?>' />
    		    <input type='hidden' name='IDTicket' id='IDTicket' value='<?= $IDTicket ?>' />
    		    <? if (TemplateHandler::IsSupporter()) : ?>
      		    <select class='inputCombo' id='cannedAnswers'>
              <? if ($ArResponses[0]['IDCannedResponse'] != ''): ?>
                  <? foreach ($ArResponses as $Response): ?>
                    <option value="<?=$Response['StAlias'];?>"><?=$Response['StTitle']?></option>
                  <?endforeach; ?>
              <? else: ?>
                   <option value='null'><?=NO_ANSWER?></option>
              <? endif; ?>
      			  </select>
      			  <button class='button' onclick='addCannedResponse(); return false;'>Incluir</button>
    			  <? endif; ?>
    		</div>
    	</div>

    	<div>
    	    <button class='button' onclick='alert("To esperando a flow"); return false;'>Visualizar</button>
    			<input type='submit' class='button' value='Responder' name='Responder'>
    	</div>
    </div>
    </form>
  </div>
</div>
<!--[/TICKET ANSWER]-->