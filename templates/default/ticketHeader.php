<div id="ticketTitle">
  <img id='reloadHeader' class='menuRefresh Right' onclick='refreshCall( <?= $IDTicket ?> )' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' />
	<img alt="Ticket" id='arrowHeader' src="<?= TEMPLATEDIR ?>images/arrow_hide.gif" onclick='toogleArrow( this.id, "ticketContent")' class='menuArrow'/>
	<span><?= $StTitle ?></span>
</div>


<div id="ticketContent">
	<table class='tableTickets'>
    <thead>
      <tr>
        <th>ID</th>
        <th>Data</th>
        <th>Status</th>
        <th>Atendente</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class='TicketNumber'>#<?= $IDTicket ?></td>
        <td><?= $DtOpened ?></td>
        <td><?= $StSituation ?></td>
        <td>
        	<select id='StSupporter' onchange='setTicketOwner(<?= $IDTicket ?>, this.value)' class='inputCombo'>
        	  <? foreach ( $ArSupporters as $IDSupporter => $StSupporter ) : ?>
          	  <? if ($ArHeaders['IDSupporter'] != $IDSupporter) : ?>
          	  <option value=<?=$IDSupporter?>><?=$StSupporter?></option>
          		<? else : ?>
          		<option selected='selected' value=<?=$IDSupporter?>><?=$StSupporter?></option>
          		<? endif; ?>
        		<? endforeach; ?>
        	</select>
        </td>
      </tr>
    </tbody>
  </table>
</div>
