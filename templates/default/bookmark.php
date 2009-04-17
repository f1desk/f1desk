<?
  require_once(dirname(__FILE__) . '/../../homeData.php');
  handleLanguage(__FILE__);
?>
<h3>
  <?=BOOK_MARK?>
  <div id="bookmarkResponseLoading" class="loading hidden">
    <img src="<?=TEMPLATEDIR?>images/loading.gif" />Carregando ...
  </div>
</h3>
<table class="tableTickets homeTable" id="bookmarkTable">
	<thead>
		<th><?=ID_TICKET?></th>
		<th><?=BOOKMARK_TITLE?></th>
		<th width="20%"><?=BOOKMARK_ACTIONS?></th>
	</thead>
	<tbody>
    <? if (count($ArBookmark)!= 0): ?>
      <? foreach ($ArBookmark as $Alt => $ArBookmarkOptions): ?>
        <tr class="<?=(($Alt%2)==0)?'Alt':''?>">
          <td class="TicketNumber"><?=$ArBookmarkOptions['IDTicket']?></td>
          <td><?=$ArBookmarkOptions['StTitle']?></td>
          <td>
            <div>
              <img src="<?=TEMPLATEDIR?>images/button_cancel.png" onclick="Home.removeBookmark('<?=$ArBookmarkOptions['IDTicket']?>')">
              <img src="<?=TEMPLATEDIR?>images/visualizar.png" onclick='flowWindow.previewTicket("<?=$ArBookmarkOptions['IDTicket']?>");'>
            </div>
          </td>
        </tr>
      <? endforeach; ?>
    <? else: ?>
      <tr>
        <td align="center" colspan="3"><?=NO_BOOKMARK?></td>
      </tr>
    <? endif; ?>
	</tbody>
</table>