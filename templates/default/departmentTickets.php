<?
  handleLanguage(__FILE__);
  require_once('departmentTicketData.php');
  require_once('header.php');
?>

<div id='contentDepartments' class='Left'>
  <!-- FAZER COM A TEMPLATE HANDLER -->
  <? foreach ($ArDepartments as $ID => $StDepartment) : ?>
    <div id="departmentWrapper<?=$ID?>">
      <div id='menuTitle<?=$ID?>' class='departmentRows'>
        <img id='reload<?=$ID?>' class='menuRefresh Right' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' onclick="Ticket.reloadTicketList('<?=$ID?>');" />
        <img id='arrow<?=$ID?>' class='menuArrow' src='<?= TEMPLATEDIR ?>images/arrow_show.gif' alt='Show' onclick="Ticket.showDepartmentTickets('<?=$ID?>')"/>
        <span class='TxPadrao'><?= $StDepartment ?></span>
        <? if ($ID != 'closed' && $ID != 'ignored') :?>
      	  <span> - </span>
    	    <span class='TxDestaque'>
      		  <span id="notReadCount<?=$ID?>">
    			    <?= $ArTickets[$ID]['notReadCount'] ?>
      		  </span>
      		  <?=UNREAD?>
    		  </span>
    		<? endif; ?>
      </div>
      <div style='display:none;' id="departmentContent<?=$ID?>">
        <? require('ticketList.php'); ?>
      </div>
    </div>
  <? endforeach; ?>
  <!-- FAZER COM A TEMPLATE HANDLER -->
</div>

<div id='contentDisplay' class='Right'>
  <? if (array_key_exists('id',$_GET)) : ?>
    <? require_once('ticket.php'); ?>
    <script type="text/javascript">baseActions.selectFromSearch(<?= $_GET['id']; ?>)</script>
  <? endif; ?>
</div>

<? require_once('footer.php'); ?>