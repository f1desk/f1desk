<?php
  require_once( dirname(__FILE__) . '/../../ticketData.php' );
  /*default language*/
  handleLanguage(__FILE__);
  #
  # join arrow's ID and Content's ID with this UID
  #
  $uid = uniqid();
  $IDSupporter = getSessionProp('IDSupporter');
?>

<!--[ERROR/OK BOX]-->
<? if(isset($returnMessage) && isset($returnType)): ?>
  <div class="boxmsg <?=$returnType?>">
    <?=$returnMessage ?>
  </div>
<? endif; ?>
<!--[ERROR/OK BOX]-->

<!--[TICKET HEADER]-->
<div id='ticketHeader'>
  <div id="ticketTitle">
  <?if(!$preview):?>
    <img id='reloadHeader' class='menuRefresh Right' onclick='Ticket.refreshTicket("<?=$IDTicket?>", "<?=$IDDepartment?>")' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' />
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
          <?if(is_numeric($IDDepartment)):?>
            <th><?=TICKET_HEADER_DEPARTMENT?></th>
          <?endif;?>
          <th><?=TICKET_HEADER_SUPPORTER?></th>
          <? if ($isSupporter && !$preview): ?>
            <th colspan='3'><?=TICKET_HEADER_ACTIONS?></th>
          <? endif; ?>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class='TicketNumber'>#<?= $IDTicket ?></td>
          <td><?= $DtOpened ?></td>
          <td><?= constant($StSituation) ?></td>
          <?if(is_numeric($IDDepartment)):?>
            <td><?=TemplateHandler::createHeaderDepartmentCombo($ArDepartments, $IDDepartment, $IDTicket,'Departments', 'inputCombo',$preview);?></td>
          <?endif;?>
          <td>
            <?if ($isSupporter) : ?>
              <?= TemplateHandler::createSupportersCombo($IDTicket, $IDDepartment, $ArSupporters, $ArHeaders, 'StSupporter','inputCombo', $preview); ?>
            <? else : ?>
              <?= $StSupporter ?>
            <? endif ?>
          </td>
          <? if ($isSupporter && !$preview): ?>
          <td>
            <a href='javascript:void(0);' onclick='Ticket.attachTicket(<?=$IDTicket?>,"<?=$IDDepartment?>");'>
              <img src='<?= TEMPLATEDIR ?>images/attach.png' alt='Attach Ticket' title='Attach Ticket'>
            </a>
          </td>
          <td>
              <? if ($BoIgnored): ?>
            <a href='javascript:void(0);' onclick='Ticket.unignoreTicket(<?=$IDSupporter?>,<?=$IDTicket?>, "<?=$IDDepartment?>")'>
              <img src='<?= TEMPLATEDIR ?>images/unignore.png' alt='Ignore Call' title='Ignore Call'>
              <? else: ?>
            <a href='javascript:void(0);' onclick='Ticket.ignoreTicket(<?=$IDSupporter?>,<?=$IDTicket?>, "<?=$IDDepartment?>")'>
              <img src='<?= TEMPLATEDIR ?>images/ignore.png' alt='Ignore Call' title='Ignore Call'>
              <? endif;?>
            </a>
          </td>
          <td>
            <a href='javascript:void(0);' onclick='Ticket.bookmarkTicket(<?=$IDSupporter?>,<?=$IDTicket?>, "<?=$IDDepartment?>")'>
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

    <? if ($isSupporter) : ?>
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

    <? endif; ?>

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
    <?= TemplateHandler::showHistory($ArMessages, $ArAttachments); ?>
  </div>
</div>
<!--[/TICKET HISTORY]-->

<!--[TICKET ANSWER]-->
<?if(!$preview) :?>
	<div id='ticketAnswer' class='defaultBody'>
	  <div id='answerCaption' class='defaultCaption'>
	  	<img alt="Ticket" id='arrowAnswer<?=$uid?>' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='baseActions.toogleArrow( this.id, "answerContent<?=$uid?>")' class="menuArrow"/>
	  	<span><?=ANSWER?></span>
	  </div>

	  <div id="answerContent<?=$uid?>" >
	    <form method="POST" id="formAnswer" target="ajaxSubmit" action="<?=TEMPLATEDIR?>ticket.php" enctype="multipart/form-data" onsubmit='this.didSubmit.value="true"; if(isEmpty(gID("TxMessage").value)){ flowWindow.alert(i18n.answerPreviewNoAnswer); return false; }'>
	      <div id='messageType' class='Right'>
	    	  <select name='StMessageType' id='StMessageType' class='inputCombo'>
	    				<option value="NORMAL"><?=MSGTYPE_NORMAL?></option>
	    				<? if ($isSupporter): ?>
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
	      			<iframe onLoad="top.Ticket.submitTicketForm();" id='ajaxSubmit' name='ajaxSubmit' src='<?=TEMPLATEDIR?>ticket.php' class="Invisible"></iframe>
	      		</div>
	    		  <div>
	    		    <input type='hidden' name='IDDepartment' id='IDDepartment' value='<?= $IDDepartment ?>' />
	    		    <input type='hidden' name='IDTicket' id='IDTicket' value='<?= $IDTicket ?>' />
	    		    <? if ($isSupporter) : ?>
	    		     <?=TemplateHandler::createCannedCombo($ArResponses)?>
	    		    <? endif; ?>
	    		</div>
	    	</div>

	    	<div>
	    	    <input type='hidden' name='StAction' value='answer'>
	    	    <input type="hidden" id='didSubmit' name='didSubmit' value="false">
    	      <input type='submit' class='button' value='<?=ANSWER?>' name='Responder'>
    	      <button type="button" class='button' onclick='flowWindow.previewAnswer(gID("TxMessage").value, <?=$IDTicket?>, "<?=$IDDepartment?>", gID("StMessageType").value);'><?=PREVIEW?></button>
	    	</div>
	    </div>
	    </form>
	  </div>
	</div>
<?endif;?>
<!--[/TICKET ANSWER]-->