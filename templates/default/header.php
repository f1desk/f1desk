<?php
  /*default*/
  handleLanguage(__FILE__);

	$currentPage = TemplateHandler::$CurrentPage;

	if ( file_exists(ABSTEMPLATEDIR . $currentPage . '.php') && Validate::Session(true) === true ) {
		$ArMenu = TemplateHandler::getMenuTab( $currentPage );
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
   </head>

    <body>
      <div id='header'>
        <div class='Left' id='logo'>
          <img src='images/f1desk_logo.png' alt='F1Desk'/>
        </div>

        <div class='Right' id="search_box">
	        <input type="text" class='Left' id="search_text" value="Busca" onclick='if (this.value =="Busca") { this.value=""; }' onblur='if (this.value =="") { this.value="Busca"; }'/>
	        <input type="image" class='Right' id="search_image" src="<?= TEMPLATEDIR ?>images/btn_search_box.gif" alt="Search" title="Search" />
				</div>
      </div>

      <div class='Left' id='main'>
        <div class='Left' id='shadow'>
          <div class='Left' id='content'>
            <div class='Left' id='contentHeader'>
            	<? if ( count($ArMenu) != 0 ): ?>
	              <ul>
	              	<?foreach ($ArMenu as $ArMenuSettings):?>
									<li>
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