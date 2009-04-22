<?php require_once(dirname(__FILE__).'/../../../adminData.php');?>
<div id='adminWrapper'>
  <div id='manageOptions' class='Left'>
      <?=ErrorHandler::getNotice('option');?>
      <h3>Gerenciar Preferências</h3>
      <table class='tableTickets'>
        <thead>
          <th>Op&ccedil;&otilde;es</th>
          <th>Valores</th>
          <th colspan='2'>A&ccedil;&otilde;es</th>
        </thead>
        <tbody>
          <? $i = 0; ?>
          <? foreach ($ArGeneralOptions as $StOptionName => $StOptionValue) : ?>
            <? $i++; ?>
            <? $StClass = ($i % 2 == 0) ? 'alt' : ''; ?>
            <tr class=<?= $StClass?>>
              <td><?= $StOptionName ?></td>
              <td><?= $StOptionValue ?></td>
              <td>
                <a href="javascript:void(0);" onclick="">
                  <img src="templates/default/images/button_edit.png"/>
                </a>
              </td>
            </tr>
          <? endforeach; ?>
        </tbody>
      </table>
  </div>
</div>