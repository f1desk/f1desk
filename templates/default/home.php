<?php
/*default language*/
handleLanguage(__FILE__);
require_once('header.php');
?>
<div id="homeTemplate">

  <!-- First Box: User Datas -->
  <?require_once(TEMPLATEDIR . 'userData.php')?>
  <!-- First Box End -->

  <!-- Third Box: Supporter Canned Responses -->
  <?require_once(TEMPLATEDIR . 'cannedResponses.php');?>
  <!-- Third Box End -->

  <!-- Fourth Box: Supporter Notes -->
  <?require_once(TEMPLATEDIR . 'notes.php')?>
  <!-- Fourth Box End -->

  <!-- Second Box: Supporter Bookmark -->
  <?require_once(TEMPLATEDIR . 'bookMark.php');?>
  <!-- Second Box End -->

</div>
<?php
require_once('footer.php');
?>