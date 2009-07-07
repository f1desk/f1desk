<?php
  include(dirname(__FILE__) . '/../../../reportData.php');
?>
<div id="reportWrapper">
  <h3> Respostas por departamento </h3>
  <table class="tableTickets">
    <thead>
      <th>Departamento</th>
      <th>Total</th>
    </thead>
    <tbody>
      <?foreach ($ArAnswersByDepartment as $Alt => $ArAnswersByDepartmentOptions):?>
        <tr class="<?=($Alt % 2 != 0)?'Alt':''?>">
          <td class="TicketNumber"><?=$ArAnswersByDepartmentOptions['StDepartment']?></td>
          <td class="IntValue"><?=$ArAnswersByDepartmentOptions['ItTotal']?></td>
        </tr>
      <?endforeach;?>
    </tbody>
  </table>
</div>