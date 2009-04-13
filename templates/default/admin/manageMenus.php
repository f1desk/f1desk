<?php require_once(dirname(__FILE__).'/../../../adminData.php');?>
<div id='adminWrapper'>
  <div id='createMenu'>
  <?=ErrorHandler::getNotice();?>
    <h3>Cadastrar Menu</h3>
    <div id='newMenuData'>
      Nome:
      <p><input type='text' id='StName' name='StName' class='inputCombo' /></p>
      Endere&ccedil;o:
      <p><input type='text' id='StAddress' name='StAddress' class='inputCombo' /></p>
      <p>
        <button class='button' onclick='Admin.insertMenu();'>Cadastrar</button>
        <button class='button'>Limpar</button>
      </p>
    </div>
  </div>
  <div id='manageMenu' class='Left'>
      <h3>Gerenciar Menus</h3>
      <table class='tableTickets'>
        <thead>
          <th>Menus Cadastrados</th>
          <th colspan='2'>A&ccedil;&otilde;es</th>
        </thead>
        <tbody>
          <?=TemplateHandler::showMenus($ArMenus);?>
        </tbody>
      </table>
  </div>
  <div id='editMenu' class='Left Invisible'>
  <h3>Editar Menu</h3>
    Nome:
    <p><input type='text' id='StNameEdit' name='StName' class='inputCombo' /></p>
    Endere&ccedil;o:
    <p><input type='text' id='StAddressEdit' name='StAddress' class='inputCombo' /></p>
    <p>
      <button class='button' onclick='Admin.editMenu(gID("StAddressEdit").value);'>Cadastrar</button>
      <button class='button' onclick='Admin.hideEditMenu();'>Limpar</button>
      <input type='hidden' id='StOldAddressEdit' name='StOldAddressEdit' />
    </p>
  </div>
</div>