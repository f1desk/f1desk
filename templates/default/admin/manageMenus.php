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
  <div id='manageMenu'>
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
</div>