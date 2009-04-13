<?php
  /*default language*/
  handleLanguage(__FILE__);
  require_once('main.php');
  require_once('header.php');
?>
<div id='homeTemplate'>

  <!-- First Box: User Data -->
  <div id='dataBox' class='homeBox'>
    <?require_once(TEMPLATEDIR . 'userInfo.php')?>
  </div>
  <!-- First Box End -->
  
  <?if (F1DeskUtils::IsSupporter()) :?>
    <!-- Second Box: Supporter Canned Responses -->
    <div id="cannedResponsesBox" class="homeBox">
      <?require_once(TEMPLATEDIR . 'cannedResponses.php');?>
    </div>
    <!-- Second Box End -->
    
    <!-- Third Box: Supporter Notes -->
    <div id="notesBox" class="homeBox">
      <?require_once(TEMPLATEDIR . 'notes.php')?>
    </div>
    <!-- Third Box End -->
      
    <!-- Fourth Box: Supporter Bookmark -->
    <div id="bookmarkBox" class="homeBox">
      <?require_once(TEMPLATEDIR . 'bookmark.php');?>
    </div>
    <!-- Fourth Box End -->
  <? endif; ?>

</div>
<?php require_once('footer.php'); ?>