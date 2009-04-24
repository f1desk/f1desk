<?php
  /*default*/
  handleLanguage(__FILE__);

	$currentPage = F1DeskUtils::$CurrentPage;

	if ( file_exists(ABSTEMPLATEDIR . $currentPage . '.php') && Validate::Session(true) === true ) {
		$ArMenu = F1DeskUtils::getMenuTab( $currentPage );
	} else {
		$ArMenu = array();
	}

 ?>
<? print('<?xml version="1.0" encoding="UTF-8"?>' . "\n"); ?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
    <head>
      <title><?= getOption('title'); ?></title>
      <style type="text/css" media="screen">
         @import url('<?= TEMPLATEDIR ?>css/style.css');
         @import url('<?= TEMPLATEDIR ?>css/fonts.css');
      </style>
      <?= defaultCSS() ?>
      <?= defaultJS() ?>
      <script type='text/javascript' src='<?= TEMPLATEDIR ?>js/lang/<?=getCurrentLanguage()?>/i18n.js'></script>
      <script type='text/javascript' src='<?= TEMPLATEDIR ?>js/default.js'></script>
   </head>

    <body>
      <div id='header'>
        <div class='Left' id='logo'>
          <img src='images/logotipo.jpg' alt='F1Desk'/>
        </div>

        <? if (Validate::Session(true)) : ?>
        <div class='Right' id="search_box">
          <form name='formSearch' action='index.php' method='GET' onsubmit='return baseActions.validateQuickSearch(this);'>

  	        <input type="text" name='id' class='Left' id="search_text" />
  	        <img class='Right' id="search_image" src="<?= TEMPLATEDIR ?>images/btn_search_box.gif" alt="Search" title="Search" onclick='formSearch.onsubmit();'/>
            <input type='hidden' name='page' value='departmentTickets' />

	        </form>
				</div>
				<? endif; ?>
      </div>

      <div class='Left' id='main'>
        <div class='Left' id='shadow'>
          <div class='Left' id='content'>
            <div class='Left' id='contentHeader'>
            	<? if ( count($ArMenu) != 0 ): ?>
	              <ul id='menuList'>
	              	<?foreach ($ArMenu as $ArMenuSettings):?>
									<li id='<?=$ArMenuSettings['Link']?>'>
										<span class='<?=$ArMenuSettings['Current']?>'>
											<a href='?page=<?=$ArMenuSettings['Link']?>'>
												<?=$ArMenuSettings['Name']?>
											</a>
										</span>
									</li>
	                <?endforeach;?>
	              </ul>
              <? endif; ?>
            </div>
            <div class='Left' id='contentWrapper'>