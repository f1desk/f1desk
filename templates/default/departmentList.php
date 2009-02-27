<?php
  /*default*/
  handleLanguage(__FILE__);

  $IDUser = getSessionProp("IDUser");

  if (getSessionProp( 'isSupporter' ) == 'true') {
  	$ArDepartments = TemplateHandler::getDepartments( $IDUser, true );
    $StUserType = 'supporter';
  } else {
    $ArDepartments = TemplateHandler::getUserDepartments();
    $StUserType = 'client';
  }

  $ArNotRead = TemplateHandler::notReadCount( $IDUser, $StUserType );

?>

<? foreach ($ArDepartments as $ID => $ArDepartment) : ?>
  <div id="departmentWrapper<?=$ID?>">
    <div id='menuTitle<?=$ID?>' class='departmentRows'>
      <img id='reload<?=$ID?>' class='menuRefresh Right' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' onclick="reloadTicketList('<?=$ID?>');" />
      <img id='arrow<?=$ID?>' class='menuArrow' src='<?= TEMPLATEDIR ?>images/arrow_show.gif' alt='Show' onclick="showDepartmentTickets('<?=$ID?>')"/>
      <span class='TxPadrao'><?= $ArDepartment['StDepartment'] ?></span>
      <? if ($ID != 'closed') :?>
    	  <span> - </span>
  	    <span class='TxDestaque'>
    		  <span id="notReadCount<?=$ID?>">
  			    <?= $ArNotRead[$ID] ?>
    		  </span>
    		  <?=TO_READ?>
  		  </span>
  		<? endif; ?>
    </div>
    <div style='display:none;' id="departmentContent<?=$ID?>"></div>
  </div>
<? endforeach; ?>