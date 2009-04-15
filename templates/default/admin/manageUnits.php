<?php
  require_once( dirname(__FILE__) . '/../../../adminData.php');
  handleLanguage(__FILE__);
?>
<div id="adminWrapper">
  <?=ErrorHandler::getNotice('unit');?>
  <div id="insertUnit">
    <h3><?=INSERT_UNIT?></h3>
    <form name="insertUnitForm" id="insertUnitForm" onsubmit="return false;">
      <?=NAME?>:
      <p>
        <input type="text" name="name" class="inputCombo" />
      </p>
      
      <p>
        <ul class="permissionUL">
          <li>
            <table class="tableTickets">
              <thead>
                <th><?=PERMISSION_TO?></th>
                <th></th>
              </thead>
              <tr>
                <td><?=ANSWER_TICKET?></td>
                <td><input type="checkbox"></td>
              </tr>
              <tr>
                <td><?=ATTACH_TICKET?></td>
                <td><input type="checkbox"></td>
              </tr>
              <tr>
                <td><?=CREATE_TICKET?></td>
                <td><input type="checkbox"></td>
              </tr>
              <tr>
                <td><?=DELETE_TICKET?></td>
                <td><input type="checkbox"></td>
              </tr>
            </table>
          </li>
          <li>
            <table class="tableTickets">
              <thead>
                <th><?=PERMISSION_TO?></th>
                <th></th>
              </thead>
              <tr>
                <td><?=VIEW_TICKET?></td>
                <td><input type="checkbox"></td>
              </tr>
              <tr>
                <td><?=RELEASE_ANSWER?></td>
                <td><input type="checkbox"></td>
              </tr>
              <tr>
                <td><?=MAIL_ERROR?></td>
                <td><input type="checkbox"></td>
              </tr>
              <tr>
                <td><?=CANNED_RESPONSES?></td>
                <td><input type="checkbox"></td>
              </tr>
            </table>
          </li>
        </ul>
      </p>
      
      <p>
        <button class='button'><?=SAVE?></button>
        <button class='button' type="reset"><?=CLEAR?></button>
      </p>
    </form>
  </div>
  <div id="manageUnit" class="Left" style="width:50%">
    <h3><?=MANAGE_UNITS?></h3>
    <table class="tableTickets" style="width:90%">
      <thead>
        <th><?=UNIT?></th>
        <th><?=ACTIONS?></th>
      </thead>
      <tbody>
        <?//ErrorHandler::debug($ArUnits);?>
        <?if (count($ArUnits)!=0):?>
          <?foreach ($ArUnits as $ArUnitsOptions):?>
            <tr class="Alt">
              <td><?=$ArUnitsOptions['StUnit']?></td>
              <td>
                <a href="javascript:void(0);" onclick="">
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
        <?else:?>
          <tr><td align="center" colspan="2"><?=NO_UNITS?></td></tr>
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