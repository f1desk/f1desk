<?
require_once('header.php');
require_once('createData.php');
handleLanguage(__FILE__);

?>

<div id='contentDisplay' class='Right'></div>

<div id='createWrapper'>
  <form id='formCreate' method='POST' enctype='multipart/form-data' action='<?$PHP_SELF?>' onsubmit='return Writing.createTicketSubmit();'>
  <?php if (F1DeskUtils::IsSupporter()):?>
    <h3><?=TICKET_TYPE?></h3>
    <?=TemplateHandler::showTicketTypes($ArTypes);?>

    <div id='AttachTickets'>
      <h3><?=ATTACH_TICKET?></h3>
      <p><?=CLICK?><a href='javascript:void(0)' class='Link' onclick='Writing.attachTicket()'><?=HERE?></a><?=TO_ATTACH?></p>
      <div id='AttachedTickets' class='Invisible'>
        <h4><?=ATTACHED_TIC?></h4>
      </div>
    </div>
    <?php endif; ?>
    <h3><?=TICKET_INFO?></h3>
    <p>
      <label for='StCategory'><?=CATEGORY?></label>
      <?=TemplateHandler::createCategory_PriorityCombobox($ArCategories,'StCategory','StCategory','inputCombo');?>
    </p>
    <p>
      <label for='StPriority'><?=PRIORITY?></label>
      <?=TemplateHandler::createCategory_PriorityCombobox($ArPriorities,'StPriority','StPriority','inputCombo');?>
    </p>
    <div id='sendTo'>
      <h3><?=SEND_TO?></h3>
      <?=TemplateHandler::createFormattedCombo($ArDepartments,'IDRecipient','IDRecipient','inputCombo');?>
      <?php if (F1DeskUtils::IsSupporter()):?>
      <p><?=CLICK?> <a href='javascript:void(0);' class='Link' onclick='Writing.listSupporters("Recipients")'><?=HERE?></a><?=ADD_SUPPORTER?></p>
      <div id='addedRecipients' class='Invisible'>
        <h4><?=ADDED_SUP?></h4>
      </div>
      <?php endif; ?>
    </div>

    <?php if (F1DeskUtils::IsSupporter()):?>
    <div id='respondTo'>
      <h3><?=REPLY_TO?></h3>
      <?=TemplateHandler::createFormattedCombo($ArDepartments,'IDReader','IDReader','inputCombo');?>
      <p><?=CLICK?><a href='javascript:void(0);' class='Link' onclick='Writing.listSupporters("Readers")'><?=HERE?></a><?=ADD_SUPPORTER?>
      <div id='addedReaders' class='Invisible'>
        <h4><?=ADDED_SUP?></h4>
      </div>
    </div>
    <?php endif; ?>
    <h3><?=MESSAGE?></h3>
    <p>
      <label for='StTitle'><?=TITLE?></label>
      <input type='text' id='StTitle' name='StTitle' class='inputFile'>
    </p>
    <p>
      <label for='TxMessage'><?=MESSAGE?>:</label>
      <textarea id='TxMessage' name='TxMessage' class='answerArea'></textarea>
    </p>
    <p class='Right'>
      <input type='file' id='Attachment' name='Attachment' />
    </p>
    <p class='Left'>
      <button class='button'><?=CREATE?></button>
    </p>
    <input type='hidden' name='StAction' value='create' />
  </form>
</div>
<? require_once('footer.php'); ?>
<?php if (isset($IDTicket)):?>
  <script>javascript:void(flowWindow.previewTicket('<?=$IDTicket;?>'))</script>
<?php endif; ?>