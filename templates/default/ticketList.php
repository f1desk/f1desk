<?
  if (array_key_exists('IDDepartment',$_POST)) {
    require_once(dirname(__FILE__) . '/../../departmentTicketData.php');
    $ID = $_POST['IDDepartment'];
  }

?>
<table id='ticketTable<?=$ID?>' class='tableTickets'>
  <thead>
    <tr>
      <th style="cursor:pointer;" onclick="TICKET.orderTicketList(0, 'ticketTable<?=$IDDepartment?>')">N&uacute;mero</th>
      <th style="cursor:pointer;" onclick="TICKET.orderTicketList(1, 'ticketTable<?=$IDDepartment?>')">Nome</th>
      <th style="cursor:pointer;" onclick="TICKET.orderTicketList(2, 'ticketTable<?=$IDDepartment?>')">Atendente</th>
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
        <td onclick='TICKET.showTicket( <?=$Ticket['IDTicket']?>, "<?=$IDDepartment?>", this.parentNode )' id='<?= $ID ?>' class='TicketNumber'>
          #<?= $Ticket['IDTicket']?>
        </td>
        <td onclick='TICKET.showTicket( <?=$Ticket['IDTicket']?>, "<?=$IDDepartment?>", this.parentNode )'>
          <?= $Ticket['StTitle']?>
        </td>
        <td onclick='TICKET.showTicket( <?=$Ticket['IDTicket']?>, "<?=$IDDepartment?>", this.parentNode )' id='TicketSupporter<?= $Ticket['IDTicket']?>'>
          <?= $Ticket['StSupporter']?>
        </td>
        <td onclick="flowWindow.previewTicket(<?=$Ticket['IDTicket']?>);" style='text-align:center;'>
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