<?require_once('header.php');?>
<div id='reportMenu'>
  <ul id='Menu'>
    <li>
      <a href='javascript:void(0);' onclick="Report.changeOption('ticketByDepartments')">Volume de chamados por departamento</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Report.changeOption('answerByDepartments')">Volume de respostas por departamento</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Report.changeOption('answerBySupporters')">Volume de respostas por atendente</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Report.changeOption('supportersByDepartments')">Atendentes por departamento</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Admin.changeOption('preferences.php')">Criar minha estat&iacute;stica</a>
    </li>
  </ul>
</div>
<div id='contentReportMenu'></div>
<?require_once('footer.php');?>