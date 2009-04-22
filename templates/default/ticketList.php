<?
  #
  # if it's a refresh action
  #
  if (array_key_exists('IDDepartment',$_POST)) {
    require_once(dirname(__FILE__) . '/../../departmentTicketData.php');
    $ID = $_POST['IDDepartment'];
  }
  
  #
  # Default Language
  #
  handleLanguage(__FILE__);
?>
<table id='ticketTable<?=$ID?>' class='tableTickets'>
  <thead>
    <tr>
      <th style="cursor:pointer;" width="31%" onclick="Ticket.orderTicketList(0, 'ticketTable<?=$ID?>', this.parentNode)">
        <?=TICKET_NUMBER?>
        <span class="orderTicket increasing"/>
      </th>
      <th style="cursor:pointer;" width="" onclick="Ticket.orderTicketList(1, 'ticketTable<?=$ID?>', this.parentNode)">
        <?=TICKET_TITLE?>
        <span class="orderTicket"/>
      </th>
      <th style="cursor:pointer;" width="" onclick="Ticket.orderTicketList(2, 'ticketTable<?=$ID?>', this.parentNode)">
        <?=TICKET_SUPPORTER?>
        <span class="orderTicket"/>
      </th>
      <th><?=TICKET_OPEN?></th>
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
          <?= TemplateHandler::reduceTitle($Ticket['StTitle'])?>
        </td>
        <td onclick='Ticket.showTicket( <?=$Ticket['IDTicket']?>, "<?=$ID?>", this.parentNode )' id='TicketSupporter<?= $Ticket['IDTicket']?>'>
          <?= TemplateHandler::reduceTitle($Ticket['StSupporter'])?>
        </td>
        <td onclick="flowWindow.previewTicket(<?=$Ticket['IDTicket']?>, '<?=$ID?>');" style='text-align:center;'>
          <img src="<?=TEMPLATEDIR?>images/visualizar.png">
        </td>
      </tr>
      <? endforeach; ?>
    <? else : ?>
      <tr>
        <td id="noTicket" colspan='4' style='text-align:center;'><?=NO_CALLS?></td>
      </tr>
    <? endif; ?>
  </tbody>
</table>