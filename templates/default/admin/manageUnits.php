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
        <table class="tableTickets">
          <thead>
            <th><?=PERMISSION_TO?></th>
            <th></th>
          </thead>
          <tr>
            <td><?=ANSWER_TICKET?></td>
            <td><input type="checkbox" name="BoAnswer"></td>
          </tr>
          <tr>
            <td><?=ATTACH_TICKET?></td>
            <td><input type="checkbox" name="BoAttach"></td>
          </tr>
          <tr>
            <td><?=CREATE_TICKET?></td>
            <td><input type="checkbox" name="BoCreate"></td>
          </tr>
          <tr>
            <td><?=DELETE_TICKET?></td>
            <td><input type="checkbox" name="BoDelete"></td>
          </tr>
          <tr>
            <td><?=VIEW_TICKET?></td>
            <td><input type="checkbox" name="BoView"></td>
          </tr>
          <tr>
            <td><?=RELEASE_ANSWER?></td>
            <td><input type="checkbox" name="BoRelease"></td>
          </tr>
          <tr>
            <td><?=MAIL_ERROR?></td>
            <td><input type="checkbox" name="BoMailError"></td>
          </tr>
          <tr>
            <td><?=CANNED_RESPONSES?></td>
            <td><input type="checkbox" name="BoCannedResponse"></td>
          </tr>
        </table>
      </p>
      
      <p>
        <button class='button' onclick="Admin.submitManageUnit('create');"><?=SAVE?></button>
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
        <?if (count($ArUnits)!=0):?>
          <?foreach ($ArUnits as $ArUnitsOptions):?>
            <tr class="Alt">
              <td><?=$ArUnitsOptions['StUnit']?></td>
              <td>
                <a href="javascript:void(0);" onclick="Admin.startEditingUnit('<?=$ArUnitsOptions['IDUnit']?>')">
                  <img src="templates/default/images/button_edit.png"/>
                </a>
                <a href="javascript:void(0);" onclick="">
                  <img src="templates/default/images/button_cancel.png" onclick="Admin.submitManageUnit('remove','<?=$ArUnitsOptions['IDUnit']?>')"/>
                </a>
              </td>
              <td style="display:none;">
                <input type="hidden" id="StUnit<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['StUnit']?>">
                <input type="hidden" id="BoAnswer<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoAnswer']?>">
                <input type="hidden" id="BoAttachTicket<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoAttachTicket']?>">
                <input type="hidden" id="BoCreateTicket<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoCreateTicket']?>">
                <input type="hidden" id="BoDeleteTicket<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoDeleteTicket']?>">
                <input type="hidden" id="BoViewTicket<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoViewTicket']?>">
                <input type="hidden" id="BoReleaseAnswer<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoReleaseAnswer']?>">
                <input type="hidden" id="BoMailError<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoMailError']?>">
                <input type="hidden" id="BoCannedResponse<?=$ArUnitsOptions['IDUnit']?>" value="<?=$ArUnitsOptions['BoCannedResponse']?>">
              </td>
            </tr>
          <?endforeach;?>
        <?else:?>
          <tr><td align="center" colspan="2"><?=NO_UNITS?></td></tr>
        <?endif;?>
      </tbody>
    </table>
  </div>
  
  <div id="manageEditUnit" class="Left Invisible" style="width:50%;">
    <h3><?=EDIT_UNIT?></h3>
    <?=NAME?>:
    <p>
      <input type="text" id="StUnitEdit" class="inputCombo" />
    </p>
    <p>
      <table class="tableTickets">
        <thead>
          <th><?=PERMISSION_TO?></th>
          <th></th>
        </thead>
        <tr>
          <td><?=ANSWER_TICKET?></td>
          <td><input type="checkbox" id="BoAnswerEdit"></td>
        </tr>
        <tr>
          <td><?=ATTACH_TICKET?></td>
          <td><input type="checkbox" id="BoAttachEdit"></td>
        </tr>
        <tr>
          <td><?=CREATE_TICKET?></td>
          <td><input type="checkbox" id="BoCreateEdit"></td>
        </tr>
        <tr>
          <td><?=DELETE_TICKET?></td>
          <td><input type="checkbox" id="BoDeleteEdit"></td>
        </tr>
        <tr>
          <td><?=VIEW_TICKET?></td>
          <td><input type="checkbox" id="BoViewEdit"></td>
        </tr>
        <tr>
          <td><?=RELEASE_ANSWER?></td>
          <td><input type="checkbox" id="BoReleaseEdit"></td>
        </tr>
        <tr>
          <td><?=MAIL_ERROR?></td>
          <td><input type="checkbox" id="BoMailErrorEdit"></td>
        </tr>
        <tr>
          <td><?=CANNED_RESPONSES?></td>
          <td><input type="checkbox" id="BoCannedResponseEdit"></td>
        </tr>
      </table>
    </p>
    <p>
      <input type="hidden" id="UnitID" value="">
      <button class='button' onclick="Admin.submitManageUnit('edit');"><?=SAVE?></button>
      <button class='button' onclick="gID('manageEditUnit').className='Left Invisible'"><?=CLEAR?></button>
    </p>
  </div>
  
</div>