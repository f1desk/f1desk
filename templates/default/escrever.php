<? require_once('header.php'); require_once('createTicket.php');?>

<div id='contentDisplay' class='Right'></div>

<div id='createWrapper'>
  <form method='POST' enctype='multipart/form-data' action='createTicket.submit.php' target='ajaxSubmit'>
    <h3>Anexar Chamados</h3>
    <p>Clique <a href='' class='Link'>aqui</a> para anexar chamados</p>

    <h3>Informa&ccedil;&otilde;es do chamado</h3>
    <p>
    <label for='StPriority'>Prioridade:</label>
      <select id='StPriority' name='StPriority' class='inputCombo'>
        <?php foreach ($ArCategories as $Key => $Category):?>
        <option value='<?=$Key?>'><?=$Category?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <p>
      <label for='StCategory'>Categoria:</label>
      <select id='StCategory' name='StCategory' class='inputCombo'>
        <?php foreach ($ArPriorities as $Key => $Priority):?>
        <option value='<?=$Key?>'><?=$Priority?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <h3>Enviar Para</h3>
    <select name='StDepartment' class='inputCombo'>
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
    <p>Clique <a href='' class='Link'>aqui</a> para adicionar atendentes</p>

    <h3>Responder Para</h3>
    <select size='1' name='StDepartment' class='inputCombo'>
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
    <p>Clique <a href='' class='Link'>aqui</a> para adicionar atendentes</p>
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
      <input type='file' id='AttachFile' name='AttachFile' />
      <iframe id='ajaxSubmit' name='ajaxSubmit' src='createTicket.submit.php'></iframe>
    </p>
    <p class='Left'>
      <button class='button'>Enviar</button>
      <button class='button'>Limpar</button>
    </p>
  </form>
</div>

<? require_once('footer.php'); ?>