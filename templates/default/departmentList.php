<?php
  /*default*/
  handleLanguage(__FILE__);
  
  $IDSupporter = getSessionProp('IDSupporter');
  $IDUser = getSessionProp('IDUser');

  if (TemplateHandler::IsSupporter()) {
  	$ArDepartments = TemplateHandler::getDepartments( $IDSupporter, true );
  	$ArNotRead = TemplateHandler::notReadCount( $IDSupporter );
  } else {
    $ArDepartments = TemplateHandler::getUserDepartments();
    $ArNotRead = TemplateHandler::notReadCount( $IDUser, false );
  }
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
  			    <?= $ArNotRead[$ID]['notRead'] ?>
    		  </span>
    		  <?=TO_READ?>
  		  </span>
  		<? endif; ?>
    </div>
    <div style='display:none;' id="departmentContent<?=$ID?>"></div>
  </div>
<? endforeach; ?>