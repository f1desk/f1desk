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
    <img id='reloadHeader' class='menuRefresh Right' onclick='TICKET.refreshTicket("<?= $IDTicket ?>")' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' />
  <?endif;?>
  	<img alt="Ticket" id='arrowHeader<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='baseActions.toogleArrow( this.id, "ticketContent<?=$uid?>")' class='menuArrow'/>
  	<span><?= $StTitle ?></span>
  </div>


  <div id="ticketContent<?=$uid?>">
  	<table class='tableTickets'>
      <thead>
        <tr>
          <th><?=TICKET_HEADER_ID?></th>
          <th><?=TICKET_HEADER_DATE?></th>
          <th><?=TICKET_HEADER_STATUS?></th>
          <th><?=TICKET_HEADER_DEPARTMENT?></th>
          <th><?=TICKET_HEADER_SUPPORTER?></th>
          <? if (TemplateHandler::IsSupporter() && !$preview): ?>
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
            <?=TemplateHandler::createHeaderDepartmentCombo($ArDepartments, $IDDepartment, $IDTicket,'Departments', 'inputCombo',$preview);?>
          </td>
          <td>
            <?=TemplateHandler::createSupportersCombo($IDTicket,$ArSupporters, $ArHeaders, 'StSupporter','inputCombo', $preview);?>
          </td>
          <? if (TemplateHandler::IsSupporter() && !$preview): ?>
          <td>
            <a href='javascript:void(0);' onclick='TICKET.attachTicket(<?=$IDTicket?>);'>
              <img src='<?= TEMPLATEDIR ?>images/attach.png' alt='Attach Ticket' title='Attach Ticket'>
            </a>
          </td>
          <td>
              <? if (F1DeskUtils::isIgnored($IDSupporter,$IDTicket)): ?>
            <a href='javascript:void(0);' onclick='TICKET.unignoreTicket(<?=$IDSupporter?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/unignore.png' alt='Ignore Call' title='Ignore Call'>
              <? else: ?>
            <a href='javascript:void(0);' onclick='TICKET.ignoreTicket(<?=$IDSupporter?>,<?=$IDTicket?>)'>
              <img src='<?= TEMPLATEDIR ?>images/ignore.png' alt='Ignore Call' title='Ignore Call'>
              <? endif;?>
            </a>
          </td>
          <td>
            <a href='javascript:void(0);' onclick='TICKET.bookmarkTicket(<?=$IDSupporter?>,<?=$IDTicket?>)'>
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
    <img alt="Informations"  id='arrowInformations<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_show.gif"  onclick='baseActions.toogleArrow( this.id, "informationsContent<?=$uid?>")' class="menuArrow"/>
    <span><?=INFORMATIONS?></span>
  </div>
  <div id='informationsContent<?=$uid?>' class="informationsBox" style="display:none">
    
    <table class="tableTickets">
      <thead>
        <th><?=INFO_CATEGORY?></th>
        <th><?=INFO_PRIORITY?></th>
        <? if ($StTicketType != ""): ?>
          <th><?=INFO_TYPE?></th>
        <? endif; ?>
      </thead>
      <tbody>
        <td><?=$StTicketCategory?></td>
        <td><?=$StTicketPriority?></td>
        <? if ($StTicketType != ""): ?>
          <td><?=$StTicketType?></td>
        <? endif; ?>
      </tbody>
    </table>
  
    <!--[ATTACHMENT FILES]-->
    <?=TemplateHandler::showAttachments($ArAttachments);?>
    <!--[/ATTACHMENT FILES]-->

    <!--[ATTACHED TICKETS]-->
    <?=TemplateHandler::showAttachedTickets($ArAttachedTickets);?>
    <!--[/ATTACHED TICKETS]-->
    
    <!--[TICKETS ATTACHED]-->
    <?=TemplateHandler::showTicketsAttached($ArTicketsAttached);?>
    <!--[/TICKETS ATTACHED]-->

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
  	<img alt="Ticket"  id='arrowHistory<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif"  onclick='baseActions.toogleArrow( this.id, "historyContent<?=$uid?>")' class="menuArrow"/>
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
	  	<img alt="Ticket" id='arrowAnswer<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='baseActions.toogleArrow( this.id, "answerContent<?=$uid?>")' class="menuArrow"/>
	  	<span><?=ANSWER?></span>
	  </div>

	  <div id="answerContent<?=$uid?>" >
	    <form method="POST" id="formAnswer" target="ajaxSubmit" action="answerTicket.php" enctype="multipart/form-data" onsubmit='if(isEmpty(gID("TxMessage").value)){ flowWindow.alert(i18n.answerPreviewNoAnswer); return false; }'>
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
	    		    <? if (TemplateHandler::IsSupporter()) : ?>
	    		     <?=TemplateHandler::createCannedCombo($ArResponses)?>
	    		    <? endif; ?>
	    		</div>
	    	</div>

	    	<div>
    	      <input type="hidden" name="action" value="answer">
    	      <input type='submit' class='button' value='<?=ANSWER?>' name='Responder'>
    	      <button type="button" class='button' onclick='flowWindow.previewAnswer(gID("TxMessage").value);'><?=PREVIEW?></button>
	    	</div>
	    </div>
	    </form>
	  </div>
	</div>
<?endif;?>
<!--[/TICKET ANSWER]-->