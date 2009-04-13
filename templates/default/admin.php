<?//die('<pre>'. print_r(get_included_files(),1));
require_once('header.php');?>
<div id='adminMenu'>
  <ul id='Menu'>
    <li>
      <a href='javascript:void(0);' onclick="Admin.changeOption('manageUsers.php')">Administrar Usu&aacute;rios</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Admin.changeOption('manageMenus.php')">Administrar Menus</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Admin.changeOption('manageDepartments.php')">Administrar Departamentos</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Admin.changeOption('preferences.php')">Administrar Grupos</a>
    </li>
    <li>
      <a href='javascript:void(0);' onclick="Admin.changeOption('preferences.php')">Prefer&ecirc;ncias</a>
    </li>
  </ul>
</div>
<div id='contentAdminMenu'>
</div>
<?require_once('footer.php');?>
<script>Admin.changeOption('manageUsers.php')</script>