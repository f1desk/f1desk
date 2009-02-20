<!--  [Content Departments] -->
<div class='Left' id='contentDepartments'>

  <?
  /*default*/
  handleLanguage(__FILE__);
  #
  # get departments of the supporter in session
  #
	$ArDepartments = TemplateHandler::getDepartments( getSessionProp( "IDUser" ), false );
  $IDSupporter = getSessionProp("IDSupporter");
  ?>

  <!--[Departament]-->
  <? foreach ($ArDepartments as $ArDepartment) : ?>
  <? $IDDepartment = $ArDepartment['IDDepartment']; ?>
	<div id="departmentWrapper<?=$IDDepartment?>">

    <div id='menuTitle<?=$IDDepartment?>' class='departmentRows'>
      <img id='reload<?=$IDDepartment?>' class='menuRefresh Right' src='<?= TEMPLATEDIR ?>images/btn_reload.png' alt='Reload' onclick="reloadTicketList(<?=$IDDepartment?>, 'supporter');" />
      <img id='arrow<?=$IDDepartment?>' class='menuArrow' src='<?= TEMPLATEDIR ?>images/arrow_show.gif' alt='Show' onclick='showDepartmentTickets(<?=$IDDepartment?>, "supporter")'/>
      <span class='TxPadrao'>
      	<?= $ArDepartment['StName'] ?>
    	</span>
    	<span>-</span>
    	<span class='TxDestaque'>
    		<span id="notReadCount<?=$IDDepartment?>">
    			<?=TemplateHandler::notReadCount( $IDDepartment, $IDSupporter, "supporter" );?>
    		</span>
    		<?=TO_READ?>
  		</span>
    </div>
    <!--[/Departament]-->

    <!--[Content]-->
    <div style='display:none;' id="departmentContent<?=$IDDepartment?>">
     <table id='ticketTable<?=$IDDepartment?>' class='tableTickets'>
        <thead>
          <tr>
            <th>N&uacute;mero</th>
            <th>Nome</th>
            <th>Atendente</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <!-- [Content] -->


  </div>
  <? endforeach; ?>
</div>
<!--  [/Content Departments] -->