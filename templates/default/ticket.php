<?php

/*default*/
handleLanguage(__FILE__);

?>

<div id='contentDisplay' class='Right'>
	<div id='ticketHeader'>
	 <? require_once('ticketHeader.php'); ?>
	</div>

  <div id='ticketHistory'>
		<? require_once('ticketHistory.php'); ?>
	</div>

	<div id='ticketAnswer'>
	  <? require_once('ticketAnswer.php'); ?>
	</div>
</div>
