<?php
  require_once( dirname(__FILE__) . '/../../../adminData.php');
  handleLanguage(__FILE__);
?>
<div id="adminWrapper">
  <?=ErrorHandler::getNotice();?>
  <div id="insertDepartment">
    <h3><?=INSERT_DEPARTMENT?></h3>
    <form name="insertDepartment" id="insertDepartmentForm" onsubmit="return false;">
      <?=NAME?>:
      <p>
        <input type="text" name="name" class="inputCombo" />
      </p>
      
      <?=DESCRIPTION?>:
      <p>
        <input type="text" name="description" class="inputCombo" />
      </p>
      
      <?=SUB_DEPARTMENT_OF?>:
      <p>
        <select class="inputCombo" name="subOf">
          <option value=""><?=NO_SUBDEPARTMENTS?></option>
          <?if (count($ArDepartments)!=0):?>
            <?foreach ($ArDepartments as $ArDepartmentOptions):?>
              <option value="<?=$ArDepartmentOptions['IDDepartment']?>">
                <?=$ArDepartmentOptions['StDepartment']?>
              </option>
            <? endforeach; ?>
          <? endif; ?>
        </select>
      </p>
      
      <p>
        <button class='button' onclick="Admin.submitManageDepartment('create')"><?=SAVE?></button>
        <button class='button' type="reset"><?=CLEAR?></button>
      </p>
    </form>
  </div>
  <div id="manageDepartment" class="Left" style="width:50%">
    <h3><?=MANAGE_DEPARTMENTS?></h3>
    <table class="tableTickets" style="width:90%">
      <thead>
        <th><?=DEPARTMENT?></th>
        <th><?=ACTIONS?></th>
      </thead>
      <tbody>
        <?if (count($ArDepartments)!=0):?>
          <?foreach ($ArDepartments as $ArDepartmentOptions):?>
            <tr class="Alt">
              <td><?=$ArDepartmentOptions['StDepartment']?></td>
              <td>
                <a href="javascript:void(0);" onclick="Admin.startEditingDepartment('<?=$ArDepartmentOptions['IDDepartment']?>');">
                  <img src="templates/default/images/button_edit.png"/>
                </a>
                <a href="javascript:void(0);" onclick="Admin.submitManageDepartment('remove', '<?=$ArDepartmentOptions['IDDepartment']?>');">
                  <img src="templates/default/images/button_cancel.png"/>
                </a>
              </td>
              <td style="display:none;">
                <input type="hidden" id="StDepartment<?=$ArDepartmentOptions['IDDepartment']?>" value="<?=$ArDepartmentOptions['StDepartment']?>">
                <input type="hidden" id="StDescription<?=$ArDepartmentOptions['IDDepartment']?>" value="<?=$ArDepartmentOptions['StDescription']?>">
              </td>
            </tr>
            <?if (isset($ArDepartmentOptions['SubDepartments']) && count($ArDepartmentOptions['SubDepartments']) != 0):?>
              <? foreach ($ArDepartmentOptions['SubDepartments'] as $ArSubDepartment): ?>
                <tr>
                  <td style="padding-left:10px;"><?=$ArSubDepartment['StSub']?></td>
                  <td>
                    <a href="javascript:void(0);" onclick="Admin.startEditingDepartment('<?=$ArSubDepartment['IDSub']?>');">
                      <img src="templates/default/images/button_edit.png"/>
                    </a>
                    <a href="javascript:void(0);" onclick="Admin.submitManageDepartment('remove', '<?=$ArSubDepartment['IDSub']?>');">
                      <img src="templates/default/images/button_cancel.png"/>
                    </a>
                  </td>
                  <td style="display:none;">
                    <input type="hidden" id="StDepartment<?=$ArSubDepartment['IDSub']?>" value="<?=$ArSubDepartment['StSub']?>">
                    <input type="hidden" id="StDescription<?=$ArSubDepartment['IDSub']?>" value="<?=$ArSubDepartment['StSubDescription']?>">
                  </td>
                </tr>
              <?endforeach;?>
            <?endif;?>
          <?endforeach;?>
        <?endif;?>
      </tbody>
    </table>
  </div>
  
  <div id="manageEditDepartment" class="Left Invisible" style="width:50%;">
    <h3><?=EDIT_DEPARTMENT?></h3>
    <p>
      <?=NAME?>:
    </p>
    <p>
      <input type="text" id="StDepartmentEdit" class="inputCombo">
    </p>
    <p>
      <?=DESCRIPTION?>:
    </p>
    <p>
      <input type="text" id="StDescriptionEdit" class="inputCombo">
    </p>
    <p>
      <input type="hidden" id="DepartmentID" value="">
      <button class='button' onclick="Admin.submitManageDepartment('edit');"><?=SAVE?></button>
      <button class='button' onclick="gID('manageEditDepartment').className='Left Invisible'"><?=CLEAR?></button>
    </p>
  </div>
  
</div>