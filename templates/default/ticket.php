<?php
  /*default language*/
  handleLanguage(__FILE__);
  #
  # join arrow's ID and Content's ID with this UID
  #
  $uid = uniqid();
  $IDSupporter = getSessionProp('IDSupporter');
?>
<!--[TICKET HEADER]-->
<div id='ticketHeader'>
  <div id="ticketTitle">
  <?if(!$preview):?>
    <img id='reloadHeader' class='menuRefresh Right' onclick='refreshCall("<?= $IDTicket ?>")' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' />
  <?endif;?>
  	<img alt="Ticket" id='arrowHeader<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "ticketContent<?=$uid?>")' class='menuArrow'/>
  	<span><?= $StTitle ?></span>
  </div>


  <div id="ticketContent<?=$uid?>">
  	<table class='tableTickets'>
      <thead>
        <tr>
          <th><?=TICKET_HEADER_ID?></th>
          <th><?=TICKET_HEADER_DATE?></th>
          <th><?=TICKET_HEADER_STATUS?></th>
          <th><?=TICKET_HEADER_SUPPORTER?></th>
          <th><?=TICKET_HEADER_DEPARTMENT?></th>
          <? if (TemplateHandler::IsSupporter()): ?>
          <th colspan='3'><?=TICKET_HEADER_ACTIONS?></th>
          <? endif; ?>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class='TicketNumber'>#<?= $IDTicket ?></td>
          <td><?= $DtOpened ?></td>
          <td><?= $StSituation ?></td>
          <td>
            <?=TemplateHandler::createSupportersCombo($IDTicket,$ArSupporters, $ArHeaders, 'StSupporter','inputCombo');?>
          </td>
          <td>
            <?=TemplateHandler::createHeaderDepartmentCombo($ArDepartments, $IDDepartment, $IDTicket,'Departments');?>
          </td>
          <? if (TemplateHandler::IsSupporter()): ?>
          <td>
            <a href='javascript:void(0);' onclick='attachTicket(<?=$IDTicket?>);'>
              <img src='<?= TEMPLATEDIR ?>images/attach.png' alt='Attach Call' title='Attach Call'>
            </a>
          </td>
          <td>
              <? if (F1DeskUtils::isIgnored($IDSupporter,$IDTicket)): ?>
            <a href='javascript:void(0);' onclick='unignoreTicket(<?=$IDSupporter?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/unignore.png' alt='Ignore Call' title='Ignore Call'>
              <? else: ?>
            <a href='javascript:void(0);' onclick='ignoreTicket(<?=$IDSupporter?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/ignore.png' alt='Ignore Call' title='Ignore Call'>
              <? endif;?>
            </a>
          </td>
          <td>
            <a href='javascript:void(0);' onclick='bookmarkTicket(<?=$IDSupporter?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/bookmark.png' alt='Bookmark Call' title='Bookmark Call'>
            </a>
          </td>
          <? endif; ?>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<!--[/TICKET HEADER]-->

<!--[TICKET INFORMATIONS]-->
<div id='ticketInformations' class='defaultBody'>
  <div id='informationsCaption' class='defaultCaption'>
    <img alt="Informations"  id='arrowInformations<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_show.gif"  onclick='toogleArrow( this.id, "informationsContent<?=$uid?>")' class="menuArrow"/>
    <span><?=INFORMATIONS?></span>
  </div>
  <div id='informationsContent<?=$uid?>' class="informationsBox" style="display:none">
    <!--[ATTACHMENT FILES]-->
    <?=TemplateHandler::showAttachments($ArAttachments);?>
    <!--[/ATTACHMENT FILES]-->

    <!--[ATTACHMENT TICKETS]-->
    <?=TemplateHandler::showAttachedTickets($ArAttachedTickets);?>
    <!--[/ATTACHMENT TICKETS]-->

    <!--[TICKET DEPARTMENTS]-->
    <?=TemplateHandler::showTicketDepartments($ArTicketDepartments);?>
    <!--[/TICKET DEPARTMENTS]-->

    <!--[TICKET SUPPORTERS]-->
    <?=TemplateHandler::showTicketSupporters($ArTicketDestinations);?>
    <!--[/TICKET SUPPORTERS]-->

    <!--[TICKET DEPARTMENTS]-->
    <?=TemplateHandler::showDepartmentReaders($ArTicketDepartmentsReader);?>
    <!--[/TICKET DEPARTMENTS]-->

    <!--[TICKET SUPPORTERS]-->
    <?=TemplateHandler::showSupporterReaders($ArTicketReaders)?>
    <!--[/TICKET SUPPORTERS]-->

  </div>
</div>
<!--[/TICKET INFORMATIONS]-->

<!--[TICKET HISTORY]-->
<div id='ticketHistory' class='defaultBody'>
  <div id="historyCaption" class='defaultCaption'>
  	<img alt="Ticket"  id='arrowHistory<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif"  onclick='toogleArrow( this.id, "historyContent<?=$uid?>")' class="menuArrow"/>
  	<span><?=TICKET_HISTORY?></span>
  </div>

  <div id="historyContent<?=$uid?>" >
    <?=TemplateHandler::showHistory($IDTicket, $ArAttachments);?>
  </div>
</div>
<!--[/TICKET HISTORY]-->

<!--[TICKET ANSWER]-->
<?if(!$preview):?>
	<div id='ticketAnswer' class='defaultBody'>
	  <div id='answerCaption' class='defaultCaption'>
	  	<img alt="Ticket" id='arrowAnswer<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "answerContent<?=$uid?>")' class="menuArrow"/>
	  	<span><?=ANSWER?></span>
	  </div>

	  <div id="answerContent<?=$uid?>" >
	    <form method="POST" id="formAnswer" target="ajaxSubmit" action="answerTicket.php" enctype="multipart/form-data" onsubmit='if(_isEmpty(gID("TxMessage").value)){ flowAlert(default_ptBR.answerPreviewNoAnswer); return false; }'>
	      <div id='messageType' class='Right'>
	    	  <select name='StMessageType' id='StMessageType' class='inputCombo'>
	    				<option value="NORMAL"><?=MSGTYPE_NORMAL?></option>
	    				<? if (TemplateHandler::IsSupporter()): ?>
	    				<option value="INTERNAL"><?=MSGTYPE_INTERNAL?></option>
	    				<option value="SATISFACTION"><?=MSGTYPE_SATISFACTION?></option>
	    				<? endif; ?>
	    		</select>
	      </div>

	    	<textarea id='TxMessage' name='TxMessage' cols='65' rows='33' class='answerArea'></textarea>

	      <div id='displayCommands'>
	        <div id='answerOptions'>

	      		<div class='Right' id='answerAttach'>
	      		  <label for='Attachment'><?=ATTACHMENT?>: </label>
	      			<input id='Attachment' name='Attachment' class='inputFile' type="file" value="Anexo" />
	      			<iframe id='ajaxSubmit' name='ajaxSubmit' src='answerTicket.php' class="Invisible"></iframe>
	      		</div>
	    		  <div>
	    		    <input type='hidden' name='IDDepartment' id='IDDepartment' value='<?= $IDDepartment ?>' />
	    		    <input type='hidden' name='IDTicket' id='IDTicket' value='<?= $IDTicket ?>' />
	    		    <?=TemplateHandler::createCannedCombo($ArResponses)?>
	    		</div>
	    	</div>

	    	<div>
	    	    <button type="button" class='button' onclick='previewInFlow.Answer(gID("TxMessage").value);'><?=PREVIEW?></button>
    	      <input type="hidden" name="action" value="answer">
	    			<input type='submit' class='button' value='<?=ANSWER?>' name='Responder'>
	    	</div>
	    </div>
	    </form>
	  </div>
	</div>
<?endif;?>
<!--[/TICKET ANSWER]-->