<?php
include_once('main.php');
$ArCategories = F1DeskUtils::listCategories();
$ArPriorities = F1DeskUtils::listPriorities();
$ArDepartments = TemplateHandler::getDepartments();
?>
<html>
  <body>
    INSERINDO CHAMADOS<br>
    <br>
    <form method="POST" action="openTicket.submit.php">
    <label for="categories">Categorias:</label>
    <select name="categories" size="1">
    <?php foreach ($ArCategories as $IDCategory => $StCategory):
            print "<option value='$IDCategory'>$StCategory</option>";
          endforeach?>
    </select><br>
    <label for="priorities">Prioridades:</label>
    <select name="priorities" size="1">
    <?php foreach ($ArPriorities as $IDPriority => $StPriority):
            print "<option value='$IDPriority'>$StPriority</option>";
          endforeach?>
    </select><br>
    <label for="departments">Departamentos:</label>
    <select name="departments" size="1">
    <?php
    foreach ( $ArDepartments as $IDDepartment => $ArDepartment ):
      echo "<option value='$IDDepartment'>{$ArDepartment['StName']}</option>";
      if ( count($ArDepartment['SubDepartment']) != 0 ):
        foreach ($ArDepartment['SubDepartment'] as $IDSubDepartment => $ArSubDepartment):
          echo "<option value='$IDSubDepartment'>&nbsp;&nbsp;&nbsp;{$ArSubDepartment['StName']}</option>";
        endforeach;
			endif;
    endforeach;
    ?>
      </select><br>
      T&iacute;tulo: <br>
      <input type="text" name="StTitle"><br>
      Mensagem:<br>
      <textarea name="StMessage"></textarea>
      <br><br>
      <input type="submit" name="sub" value="Criar!">
    </form>
  </body>
</html>