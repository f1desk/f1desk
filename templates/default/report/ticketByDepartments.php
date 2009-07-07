<?php
  include(dirname(__FILE__) . '/../../../reportData.php');
  function _countSituation($StSituation, $ArTicket){
    if ( !array_key_exists($StSituation, $ArTicket) ) {
    	return 0;
    } else {
      return $ArTicket[$StSituation];
    }
  }
?>
<div id="reportWrapper">
  <h3> Chamados por departamento </h3>
  <table class="tableTickets">
    <thead>
      <th>Departamento</th>
      <th>N&atilde;o Atendido</th>
      <th>Aguardando Cliente</th>
      <th>Aguardando Atendente</th>
      <th>Fechado</th>
      <th>Total</th>
    </thead>
    <tbody>
      <?foreach ($ArTicketsByDepartment as $Alt => $ArTicketsByDepartmentOptions):?>
        <tr class="<?=($Alt % 2 != 0)?'Alt':''?>">
          <td class="TicketNumber"><?=$ArTicketsByDepartmentOptions['StDepartment']?></td>
          <td class="IntValue"><?=_countSituation('NOT_READ',$ArTicketsByDepartmentOptions['ArSituation'])?></td>
          <td class="IntValue"><?=_countSituation('WAITING_USER',$ArTicketsByDepartmentOptions['ArSituation'])?></td>
          <td class="IntValue"><?=_countSituation('WAITING_SUP',$ArTicketsByDepartmentOptions['ArSituation'])?></td>
          <td class="IntValue"><?=_countSituation('CLOSED',$ArTicketsByDepartmentOptions['ArSituation'])?></td>
          <td class="IntValue TicketNumber"><?=$ArTicketsByDepartmentOptions['ItTotal']?></td>
        </tr>
      <?endforeach;?>
    </tbody>
  </table>
</div>