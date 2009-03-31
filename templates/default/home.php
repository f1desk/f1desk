<?php
/*default language*/
handleLanguage(__FILE__);
require_once('header.php');
?>
<div id="homeTemplate">

  <!-- First Box: User Datas -->
  <?require_once(TEMPLATEDIR . 'userData.php')?>
  <!-- First Box End -->

  <?if (TemplateHandler::IsSupporter()) :?>
  
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
    <?require_once(TEMPLATEDIR . 'bookMark.php');?>
    <!-- Fourth Box End -->
  
  <?endif;?>

</div>
<?php
require_once('footer.php');
?>