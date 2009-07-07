<?php
  include(dirname(__FILE__) . '/../../../reportData.php');
?>
<div id="reportWrapper" style="width:50%;">
  <h3> Atendentes por Departamentos </h3>
  <?//=die('<pre>' . print_r( $ArSupportersByDepartment,1 ))?>
  <?foreach ($ArSupportersByDepartment as $Alt => $ArDepartment):?>
    <div class="departmentRows">
      <img alt="Show" src="<?=TEMPLATEDIR?>images/arrow_show.gif" id="arrow<?=$Alt?>" class="menuArrow" onclick="baseActions.toogleArrow('arrow<?=$Alt?>', 'departmentContent<?=$Alt?>');"/>
      <span class="TxPadrao"> <?=$ArDepartment['StDepartment']?> </span>
      <span> - </span>
      <span class="TxDestaque">
        <?=count($ArDepartment['Supporter']);?> Atendente<?=(count($ArDepartment['Supporter'])==1)?'':'s'?>
      </span>
    </div>
    <div id="departmentContent<?=$Alt?>" style="display:none;">
      <table id="supporterTable<?=$Alt?>" class="tableTickets" >
        <thead>
          <th>Atendente &lt;E-mail&gt;</th>
        </thead>
        <tbody>
          <?if (count($ArDepartment['Supporter'])!=0):?>
            <?foreach ($ArDepartment['Supporter'] as $Cont => $StSupporter):?>
              <tr class="<?=($Cont % 2 != 0)?'Alt':''?>">
                <td><?=str_replace('<', '&lt;', str_replace('>', '&gt;',$StSupporter))?></td>
              </tr>
            <?endforeach;?>
          <?else:?>
            <td align="center">N&atilde;o h&aacute; atendentes</td>
          <?endif;?>
        </tbody>
      </table>
    </div>
  <?endforeach;?>
</div>