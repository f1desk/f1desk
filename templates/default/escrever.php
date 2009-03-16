<? require_once('header.php'); require_once('createTicket.php'); handleLanguage(__FILE__);?>

<div id='contentDisplay' class='Right'></div>

<div id='createWrapper'>
  <form id='formCreate' method='POST' enctype='multipart/form-data' action='createTicket.submit.php' onsubmit='return createTicketSubmit();'>
    <h3>Anexar Chamados</h3>
    <p>Clique <a href='' class='Link'>aqui</a> para anexar chamados</p>

    <h3>Informa&ccedil;&otilde;es do chamado</h3>
    <p>
    <label for='StCategory'>Categoria:</label>
      <select id='StCategory' name='StCategory' class='inputCombo'>
        <?php foreach ($ArCategories as $Key => $Category):?>
        <option value='<?=$Key?>'><?=$Category?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <p>
      <label for='StPriority'>Prioridade:</label>
      <select id='StPriority' name='StPriority' class='inputCombo'>
        <?php foreach ($ArPriorities as $Key => $Priority):?>
        <option value='<?=$Key?>'><?=$Priority?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <div id='sendTo'>
      <h3>Enviar Para</h3>
      <select id='IDRecipient' name='IDRecipient' class='inputCombo'>
      <option value='null'><?=DEFAULT_OPTION?></option>
      <?php foreach ($ArDepartments as $ArDepartment): ?>
        <?php if(isset($ArDepartment['SubDepartments'])): ?>
          <option value='<?=$ArDepartment['IDDepartment']?>'><?=$ArDepartment['StDepartment']?></option>
          <optgroup>
          <?php foreach ($ArDepartment['SubDepartments'] as $SubDepartments):?>
            <option value='<?=$SubDepartments['IDSub']?>'><?=$SubDepartments['StSub']?></option>
          <?php endforeach;?>
          </optgroup>
        <?php else: ?>
          <option value='<?=$ArDepartment['IDDepartment']?>'><?=$ArDepartment['StDepartment']?></option>
        <?php endif; ?>
      <?php endforeach; ?>
      </select>
      <p>Clique <a href='javascript:void(0);' class='Link' onclick='listSupporters("Recipients")'>aqui</a> para adicionar atendentes</p>
      <div id='addedRecipients' class='Invisible'>
        <h4>Usu&aacute;rios Adicionados</h4>
      </div>
    </div>

    <div id='respondTo'>
      <h3>Responder Para</h3>
      <select name='IDReader' class='inputCombo'>
      <option value='null'><?=DEFAULT_OPTION?></option>
      <?php foreach ($ArDepartments as $ArDepartment): ?>
        <?php if(isset($ArDepartment['SubDepartments'])): ?>
          <option value='<?=$ArDepartment['IDDepartment']?>'><?=$ArDepartment['StDepartment']?></option>
          <optgroup>
          <?php foreach ($ArDepartment['SubDepartments'] as $SubDepartments):?>
            <option value='<?=$SubDepartments['IDSub']?>'><?=$SubDepartments['StSub']?></option>
          <?php endforeach;?>
          </optgroup>
        <?php else: ?>
          <option value='<?=$ArDepartment['IDDepartment']?>'><?=$ArDepartment['StDepartment']?></option>
        <?php endif; ?>
      <?php endforeach; ?>
      </select>
      <p>Clique <a href='javascript:void(0);' class='Link' onclick='listSupporters("Readers")'>aqui</a> para adicionar atendentes</p>
      <div id='addedReaders' class='Invisible'>
        <h4>Usu&aacute;rios Adicionados</h4>
      </div>
    </div>
    <h3>Mensagem</h3>
    <p>
      <label for='StTitle'>T&iacute;tulo:</label>
      <input type='text' id='StTitle' name='StTitle' class='inputFile'>
    </p>
    <p>
      <label for='TxMessage'>Mensagem:</label>
      <textarea id='TxMessage' name='TxMessage' class='answerArea'></textarea>
    </p>
    <p class='Right'>
      <input type='file' id='Attachment' name='Attachment' />
    </p>
    <p class='Left'>
      <button class='button'>Enviar</button>
      <button class='button'>Limpar</button>
    </p>
  </form>
</div>
<? require_once('footer.php'); ?>
<?php if (isset($_GET['IDTicket'])):?>
  <script>javascript:void(previewInFlow.Ticket('<?=$_GET['IDTicket'];?>'))</script>
<?php endif; ?>