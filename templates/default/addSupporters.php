<div id='listSup'>
  <h2>Adicione um atendente:</h2>
  <select multiple='true' size='10' id='supporters' name='supporters'>
  <?php foreach ($ArSupporters as $ArSupporter): ?>
    <option value='<?=$ArSupporter['IDSupporter']?>' ondblclick='WRITING.checkAdd(top.Type);'><?=$ArSupporter['StName']?></option>
  <?php endforeach; ?>
  </select>
  <p>
    <button class='button' onclick='WRITING.checkAdd(top.Type);'>Adicionar</button>
  </p>
</div>