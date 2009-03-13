<?
  #
  # concatenate arrow's ID and Content's ID with this UID
  #
  $uid = uniqid();
?>
<!--[TICKET HEADER]-->
<div id='ticketHeader'>
  <div id="ticketTitle">
    <img id='reloadHeader' class='menuRefresh Right' onclick='refreshCall( <?= $IDTicket ?> )' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' />
  	<img alt="Ticket" id='arrowHeader<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "ticketContent<?=$uid?>")' class='menuArrow'/>
  	<span><?= $StTitle ?></span>
  </div>


  <div id="ticketContent<?=$uid?>">
  	<table class='tableTickets'>
      <thead>
        <tr>
          <th>ID</th>
          <th>Data</th>
          <th>Status</th>
          <th>Atendente</th>
          <?php if (TemplateHandler::IsSupporter()): ?>
          <th colspan='3'>A&ccedil;&otilde;es</th>
          <?php endif; ?>
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
          <?php if (TemplateHandler::IsSupporter()): ?>
          <td>
            <a href='javascript:void(0);' onclick='attachTicket(<?=$IDTicket?>);'>
              <img src='<?= TEMPLATEDIR ?>images/attach.png' alt='Attach Call' title='Attach Call'>
            </a>
          </td>
          <td>
              <?php if (F1DeskUtils::isIgnored(getSessionProp('IDSupporter'),$IDTicket)): ?>
            <a href='javascript:void(0);' onclick='unignoreTicket(<?=getSessionProp('IDSupporter')?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/unignore.png' alt='Ignore Call' title='Ignore Call'>
              <?php else: ?>
            <a href='javascript:void(0);' onclick='ignoreTicket(<?=getSessionProp('IDSupporter')?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/ignore.png' alt='Ignore Call' title='Ignore Call'>
              <?php endif;?>
            </a>
          </td>
          <td>
            <a href='javascript:void(0);' onclick='bookmarkTicket(<?=getSessionProp('IDSupporter')?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/bookmark.png' alt='Bookmark Call' title='Bookmark Call'>
            </a>
          </td>
          <?php endif; ?>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<!--[/TICKET HEADER]-->

<!--[TICKET ATTACHMENTS]-->
<div id='ticketActions' class='defaultBody'>
  <div id='actionsCaption' class='defaultCaption'>
    <img alt="Actions"  id='arrowActions<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif"  onclick='toogleArrow( this.id, "actionsContent<?=$uid?>")' class="menuArrow"/>
    <span>Anexos</span>
  </div>
  <div id='actionsContent<?=$uid?>'>

  </div>
</div>
<!--[/TICKET ATTACHMENTS]-->

<!--[TICKET HISTORY]-->
<div id='ticketHistory' class='defaultBody'>
  <div id="historyCaption" class='defaultCaption'>
  	<img alt="Ticket"  id='arrowHistory<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif"  onclick='toogleArrow( this.id, "historyContent<?=$uid?>")' class="menuArrow"/>
  	<span>Hist&oacute;rico</span>
  </div>

  <div id="historyContent<?=$uid?>" >

    <? foreach ($ArMessages as $ArMessage) : ?>
      <? $DtSended = F1DeskUtils::formatDate('datetime_format',$ArMessage['DtSended']); ?>
      <div class='<?= $ArMessage['StClass'] ?>'>
        <?= MSG_HEAD1 . $DtSended . MSG_HEAD2 . $ArMessage['SentBy'] . MSG_HEAD3 ?>
        <? if (array_key_exists($ArMessage['IDMessage'],$ArAttachments)): ?>
          <?  foreach ($ArAttachments[$ArMessage['IDMessage']] as $Attachment): ?>
              <p class='Link'>Anexo: <a href='download.php?IDAttach=<?=$Attachment['IDAttachment']?>'><?=$Attachment['StFile']?></a></p>
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
<?if(!$preview):?>
	<div id='ticketAnswer' class='defaultBody'>
	  <div id='answerCaption' class='defaultCaption'>
	  	<img alt="Ticket" id='arrowAnswer<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "answerContent<?=$uid?>")' class="menuArrow"/>
	  	<span>Responder</span>
	  </div>

	  <div id="answerContent<?=$uid?>" >
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
<?endif;?>
<!--[/TICKET ANSWER]-->