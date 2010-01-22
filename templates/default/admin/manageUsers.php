<?php 
  require_once(dirname(__FILE__).'/../../../adminData.php');
  handleLanguage(__FILE__);
?>
<div id='adminWrapper'>
  <div id='createSupporter' class='Left'>
    <h3><?=SUP_SINGUP?></h3>
    <div id='newSupporterData' class='Left'>
      <?=SUP_NAME?>:
      <p><input type='text' id='StName' name='StName' class='inputCombo' /></p>
      <?=SUP_EMAIL?>:
      <p><input type='text' id='StEmail' name='StEmail' class='inputCombo' /></p>
      <?=SUP_PASS?>:
      <p><input type='password' id='StPassword' name='StPassword' class='inputCombo' /></p>
      <p>
        <button class='button'><?=SINGUP?></button>
        <button class='button' onclick='Admin.clearUserForm();'><?=CLEAR?></button>
      </p>
    </div>
    <div id='SupporterDepartment' class='Left'>
      <?=SUP_DEPARTMENT?>:
      <p>
        <?=TemplateHandler::createFormattedCombo($ArDepartments,'IDDepartment','IDDepartment');?>
        <a href='javascript:void(0);' onclick='Admin.addDepartment()'><img src='templates/default/images/add.png'></a>
        <p>
          <fieldset id='Departments'>
            <legend><?=SUP_DEPARTMENTS_ADDED?></legend>
            <span id='null'><?=NO_DEPARTMENTS_ADDED?></span>
          </fieldset>
        </p>
      </p>
    </div>
  </div>
  
  <div id='manageSupporter' class='Left'>
    <h3><?=SUP_MANAGER?></h3>
    <?//=die('<pre>' . print_r( asd,1 ))?>
    <?php foreach ($ArDepartments as $ID => $ArDepartment): ?>
      <!-- DEPARTMENT -->
      <div id='menuTitle' class='departmentRows adminDptRows'>
        <img id='arrow<?=$ID?>' class='menuArrow' src='templates/default/images/arrow_show.gif' alt='Show' onclick="Ticket.showDepartmentTickets('<?=$ID?>')"/>
        <span class='TxPadrao'><?=$ArDepartment['StDepartment']?></span>
      </div>
      <div style='display:none;' id="departmentContent<?=$ID?>" class="adminDptRows">
        <table class="tableTickets">
          <thead>
            <tr>
              <th><?=SUP_NAME?></th>
              <th><?=ACTIONS?></th>
            </tr>
          </thead>
        <? if (isset($ArSupporters[$ID]) && is_array($ArSupporters[$ID]) && count($ArSupporters[$ID])>0): //Is there any supporters in this department? ?>
          <?php foreach ($ArSupporters[$ID] as $ArSupporter): ?>
            <tbody>
              <tr>
                <td><?=$ArSupporter['StName']?></td>
                <td>
                    <img style="cursor:pointer;" src="templates/default/images/button_edit.png">
                    <img style="cursor:pointer;" src="templates/default/images/button_cancel.png">
                </td>
              </tr>
            </tbody>
          <?php endforeach; ?>
        <? else: ?>
          <tbody>
            <tr>
              <td colspan="2" align="center"><?=NO_SUPPORTERS?></td>
            </tr>
          </tbody>
        <? endif; ?>
        </table>
      </div>
      <!-- /DEPARTMENT -->
      <!-- SUB-DEPARTMENT -->
      <? if (isset($ArDepartment['SubDepartments']) && is_array($ArDepartment['SubDepartments'])): ?>
        
      <? endif; ?>
      <!-- /SUB-DEPARTMENT -->
    <?php endforeach; ?>
  </div>
</div>