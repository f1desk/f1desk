<?php
  handleLanguage(__FILE__);
  require_once('departmentTicketData.php');
?>
<? foreach ($ArDepartments as $ID => $ArDepartment) : ?>
  <div id="departmentWrapper<?=$ID?>">
    <div id='menuTitle<?=$ID?>' class='departmentRows'>
      <img id='reload<?=$ID?>' class='menuRefresh Right' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' onclick="reloadTicketList('<?=$ID?>');" />
      <img id='arrow<?=$ID?>' class='menuArrow' src='<?= TEMPLATEDIR ?>images/arrow_show.gif' alt='Show' onclick="showDepartmentTickets('<?=$ID?>')"/>
      <span class='TxPadrao'><?= $ArDepartment['StDepartment'] ?></span>
      <? if ($ID != 'closed' && $ID != 'ignored') :?>
    	  <span> - </span>
  	    <span class='TxDestaque'>
    		  <span id="notReadCount<?=$ID?>">
  			    <?= $ArTickets[$ID]['notReadCount'] ?>
    		  </span>
    		  <?=TO_READ?>
  		  </span>
  		<? endif; ?>
    </div>
    <div style='display:none;' id="departmentContent<?=$ID?>">
      <? require('ticketList.php'); ?>
    </div>
  </div>
<? endforeach; ?>