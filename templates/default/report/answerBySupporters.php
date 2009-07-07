<?php
  include(dirname(__FILE__) . '/../../../reportData.php');
?>
<div id="reportWrapper">
  <h3> Respostas por atendente </h3>
  <table class="tableTickets">
    <thead>
      <th>Atendente &lt;E-mail&gt;</th>
      <th>Total</th>
    </thead>
    <tbody>
      <?foreach ($ArAnswersBySupporter as $Alt => $ArAnswersBySupporterOptions):?>
        <tr class="<?=($Alt % 2 != 0)?'Alt':''?>">
          <td><?=$ArAnswersBySupporterOptions['StName'] . '  &lt;'.$ArAnswersBySupporterOptions['StEmail'].'&gt;'?></td>
          <td class="IntValue"><?=$ArAnswersBySupporterOptions['ItTotal']?></td>
        </tr>
      <?endforeach;?>
    </tbody>
  </table>
</div>