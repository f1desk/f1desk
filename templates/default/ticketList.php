<?
  #
  # if it's a refresh action
  #
  if (array_key_exists('IDDepartment',$_POST)) {
    require_once(dirname(__FILE__) . '/../../departmentTicketData.php');
    $ID = $_POST['IDDepartment'];
  }
?>
<table id='ticketTable<?=$ID?>' class='tableTickets'>
  <thead>
    <tr>
      <th style="cursor:pointer;" onclick="Ticket.orderTicketList(0, 'ticketTable<?=$ID?>')">N&uacute;mero</th>
      <th style="cursor:pointer;" onclick="Ticket.orderTicketList(1, 'ticketTable<?=$ID?>')">Nome</th>
      <th style="cursor:pointer;" onclick="Ticket.orderTicketList(2, 'ticketTable<?=$ID?>')">Atendente</th>
      <th>Abrir</th>
    </tr>
  </thead>
  <tbody>
    <? if (! empty($ArTickets[$ID]['Tickets'])) : $i = 0; ?>
      <? foreach ($ArTickets[$ID]['Tickets'] as $Ticket) :?>
      <?
        $StClass = $Ticket['isRead'] == '0' ? 'notRead' : '';
        $StClass = ($i++ % 2 == 1) ? $StClass . ' Alt' : $StClass;
        $TdID = '';
        if (!in_array($ID,array('bookmark','single','ignored','byme'))) {
          $TdID = 'ticket' . $Ticket['IDTicket'];
        }
      ?>
      <tr style='cursor:pointer;' class='<?= $StClass ?>'>
        <td onclick='Ticket.showTicket( <?=$Ticket['IDTicket']?>, "<?=$ID?>", this.parentNode )' id='<?= $Ticket['IDTicket'] ?>' class='TicketNumber'>
          #<?= $Ticket['IDTicket']?>
        </td>
        <td onclick='Ticket.showTicket( <?=$Ticket['IDTicket']?>, "<?=$ID?>", this.parentNode )'>
          <?= $Ticket['StTitle']?>
        </td>
        <td onclick='Ticket.showTicket( <?=$Ticket['IDTicket']?>, "<?=$ID?>", this.parentNode )' id='TicketSupporter<?= $Ticket['IDTicket']?>'>
          <?= $Ticket['StSupporter']?>
        </td>
        <td onclick="flowWindow.previewTicket(<?=$Ticket['IDTicket']?>, '<?=$ID?>');" style='text-align:center;'>
          <img src="<?=TEMPLATEDIR?>images/visualizar.png">
        </td>
      </tr>
      <? endforeach; ?>
    <? else : ?>
      <tr>
        <td colspan='4' style='text-align:center;'><?= NO_CALLS ?></td>
      </tr>
    <? endif; ?>
  </tbody>
</table>