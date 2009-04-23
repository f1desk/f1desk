<?php require_once(dirname(__FILE__).'/../../../adminData.php');?>
<div id='adminWrapper'>
  <?=ErrorHandler::getNotice('option');?>
  <div id='manageOptions' class='Left'>
    <h3>Gerenciar Preferências</h3>
    <table class='tableTickets'>
      <thead>
        <th>Op&ccedil;&otilde;es</th>
        <th>Valores</th>
        <th>A&ccedil;&otilde;es</th>
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
              <a href="javascript:void(0);" onclick="Admin.startEditingOption('<?=$StOptionName?>','<?=f1desk_escape_string($StOptionValue, false, true)?>')">
                <img src="templates/default/images/button_edit.png"/>
              </a>
            </td>
          </tr>
        <? endforeach; ?>
      </tbody>
    </table>
  </div>
  <div id="manageEditOptions" class="Left Invisible">
    <h3>Editar Opção</h3>
    <form onsubmit="return false;">
      <p>Opção:</p>
      <p>
        <label id="optionEditTitle"></label>
      </p>
      <p>Valor:</p>
      <p>
        <input type="text" id="optionEditValue" class="inputCombo">
      </p>
      <p>
        <button class="button" onclick="Admin.submitManageOption()">
          Editar
        </button>
        <button class="button" onclick="gID('manageEditOptions').className += ' Invisible';">
          Limpar
        </button>
      </p>
    </form>
  </div> 
</div>