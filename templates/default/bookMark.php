<? handleLanguage(__FILE__); require_once('homeData.php');
  if ( getSessionProp('isSupporter')=="true" ):?>
		<span class="homeBoxTitle"><?=BOOK_MARK?></span>
		<span class="homeBoxTitle loadingRequest" id="bookmarkLoading"><img src="<?= TEMPLATEDIR ?>images/loading.gif"> Carregando...</span>
		<div id="bookmarkBoxContent" class="homeBoxContent">
			<table class="tableTickets" id="bookmarkTable">
				<thead>
					<th><?=ID_TICKET?></th>
					<th><?=BOOKMARK_TITLE?></th>
					<th width="20%"><?=BOOKMARK_ACTIONS?></th>
				</thead>
				<tbody>
				<?if ( count( $ArBookMark ) == 0 ):?>
					<tr id="noBookmark">
						<td colspan="3" align="center"><?=NO_BOOKMARK?></td>
					</tr>
				<?else:?>
					<?foreach ($ArBookMark as $ArBookMarkSettings):?>
						<tr id="bookmarkTR<?=$ArBookMarkSettings['IDTicket']?>">
							<td class="TicketNumber">
								#<?=$ArBookMarkSettings['IDTicket']?>
								<input type="hidden" id="StBookmarkID<?=$ArBookMarkSettings['IDTicket']?>" value="<?=$ArBookMarkSettings['IDTicket']?>">
							</td>
							<td>
								<?=$ArBookMarkSettings['StTitle']?>
								<input type="hidden" id="StBookmarkTitle<?=$ArBookMarkSettings['IDTicket']?>" value="<?=$ArBookMarkSettings['StTitle']?>">
							</td>
							<td>
								<img src="<?= TEMPLATEDIR ?>images/button_cancel.png" alt="Remover" title="Remover" class="cannedAction" onclick="Home.removeBookmark(<?=$ArBookMarkSettings['IDTicket']?>)">
								<img src="<?= TEMPLATEDIR ?>images/visualizar.png" alt="Visualizar" title="Visualizar" class="cannedAction" onclick="flowWindow.previewTicket(<?=$ArBookMarkSettings['IDTicket']?>)">
							</td>
						</tr>
					<?endforeach;?>
				<?endif;?>
				</tbody>
			</table>
		</div>
<?endif;?>