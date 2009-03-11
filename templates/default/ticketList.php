<table id='ticketTable<?=$IDDepartment?>' class='tableTickets'>
  <thead>
    <tr>
      <th>N&uacute;mero</th>
      <th>Nome</th>
      <th>Atendente</th>
      <th>Abrir</th>
    </tr>
  </thead>
  <tbody>
    <? if (! empty($ArTickets)) : $i = 0; ?>
      <? foreach ($ArTickets as $Ticket) :?>
      <?
        $StClass = $Ticket['isRead'] == '0' ? 'notRead' : '';
        $StClass = ($i++ % 2 == 1) ? $StClass . ' Alt' : $StClass;
        $ID = '';
        if ($IDDepartment != 'bookmark') {
          $ID = 'ticket' . $Ticket['IDTicket'];
        }
      ?>
      <tr style='cursor:pointer;' onclick='showCall( <?=$Ticket['IDTicket']?>, "<?=$IDDepartment?>", this )' class='<?= $StClass ?>'>
        <td id='<?= $ID ?>' class='TicketNumber'>#<?= $Ticket['IDTicket']?></td>
        <td><?= $Ticket['StTitle']?></td>
        <td id='TicketSupporter<?= $Ticket['IDTicket']?>'><?= $Ticket['StSupporter']?></td>
        <td style='text-align:center;'><img src="<?=TEMPLATEDIR?>images/visualizar.png" onclick="previewInFlow.Ticket(<?=$Ticket['IDTicket']?>);"></td>
      </tr>
      <? endforeach; ?>
    <? else : ?>
      <tr>
        <td colspan='3' style='text-align:center;'><?= NO_CALLS ?></td>
      </tr>
    <? endif; ?>
  </tbody>
</table>