<?php 
  require_once(dirname(__FILE__).'/../../../adminData.php');
  handleLanguage(__FILE__);
?>
<div id='adminWrapper'>
  
  <!--[LANGUAGE MANAGER]-->
  <div id="content2" style="width:100%;">
    <?=ErrorHandler::getNotice('language');?>
    <div id="insertLanguage">
      <h3><?=ADD_LANGUAGE?></h3>
      <form id="insertLanguageForm" onsubmit="return false;">
        <?=TITLE?>:
        <p><input type="text" name="StTitle" class="inputCombo"></p>
        <?=PATH?>:
        <p><input type="text" name="StPath" class="inputCombo"></p>
        <p>
          <button class="button" onclick="Admin.submitManageLanguage('create');"><?=ADD_LANGUAGE?></button>
          <button class="button" type="reset"><?=CLEAR?></button>
        </p>
      </form>
      
    </div>
    
    <div id='manageLanguages' class='Left'>
      <h3><?=MANAGE_LANGUAGE?></h3>
      <table class='tableTickets'>
        <thead>
          <th><?=TITLE?></th>
          <th><?=PATH?></th>
          <th><?=ACTIONS?></th>
        </thead>
        <tbody>
          <? foreach ($ArLanguages as $i => $ArLanguageOptions) : ?>
            <?
            $StClass = ($i % 2 == 0)?'alt' :''; 
            if ($ArLanguageOptions['BoSelected']){
              $StClass .= ' Selected';
            }
            ?>
            <tr class="<?=$StClass?>" >
              <td><?=$ArLanguageOptions['StTitle']?></td>
              <td><?=$ArLanguageOptions['StPath']?></td>
              <td>
                <a href="javascript:void(0);" onclick="Admin.startEditingLanguage('<?=f1desk_escape_string($ArLanguageOptions['StTitle'],false,true)?>','<?=f1desk_escape_string($ArLanguageOptions['StPath'],false,true)?>');">
                  <img src="<?=TEMPLATEDIR?>images/button_edit.png"/>
                </a>
                <a href="javascript:void(0);" onclick="Admin.removeLanguage('<?=f1desk_escape_string($ArLanguageOptions['StPath'],false,true)?>');">
                  <img src="<?=TEMPLATEDIR?>images/button_cancel.png"/>
                </a>
                <?if (!$ArLanguageOptions['BoSelected']):?>
                  <a href="javascript:void(0);" onclick="Admin.setCurrentLanguage('<?=f1desk_escape_string($ArLanguageOptions['StPath'],false,true)?>');">
                    <img src="<?=TEMPLATEDIR?>images/unignore.png"/>
                  </a>
                <?endif;?>
              </td>
            </tr>
          <? endforeach; ?>
        </tbody>
      </table>
    </div>
    <div id="manageEditLanguages" class="Left Invisible">
      <h3><?=EDIT_LANGUAGE?></h3>
      <form onsubmit="return false;">
        <p><?=TITLE?>:</p>
        <p>
          <input type="text" id="languageEditTitle" class="inputCombo">
        </p>
        <p><?=PATH?>:</p>
        <p>
          <input type="text" id="languageEditPath" class="inputCombo">
        </p>
        <p>
          <button class="button" onclick="Admin.submitManageLanguage('edit');">
            <?=EDIT_LANGUAGE?>
          </button>
          <button class="button" onclick="gID('manageEditLanguages').className += ' Invisible';">
            <?=CLEAR?>
          </button>
        </p>
      </form>
    </div>
  </div>
  <!--[/LANGUAGE MANAGER]-->
  
  <!--[OPTIONS MANAGER]-->
  <div id="content1" style="width:100%;padding-top:215px;">
    <?=ErrorHandler::getNotice('option');?>
    <div id='manageOptions' class='Left'>
      <h3><?=MANAGE_PREFERENCES?></h3>
      <table class='tableTickets'>
        <thead>
          <th><?=OPTION?></th>
          <th><?=VALUE?></th>
          <th><?=ACTIONS?></th>
        </thead>
        <tbody>
          <? $i = 0; ?>
          <? foreach ($ArGeneralOptions as $StOptionName => $StOptionValue) : ?>
            <? $i++; ?>
            <? $StClass = ($i % 2 == 0) ? 'alt' : ''; ?>
            <tr class=<?= $StClass?>>
              <td><?= $StOptionName ?></td>
              <td><?= $StOptionValue ?></td>
              <td>
                <a href="javascript:void(0);" onclick="Admin.startEditingOption('<?=$StOptionName?>','<?=f1desk_escape_string($StOptionValue, false, true)?>')">
                  <img src="<?=TEMPLATEDIR?>images/button_edit.png"/>
                </a>
              </td>
            </tr>
          <? endforeach; ?>
        </tbody>
      </table>
    </div>
    <div id="manageEditOptions" class="Left Invisible">
      <h3><?=EDIT_OPTION?></h3>
      <form onsubmit="return false;">
        <p><?=OPTION?>:</p>
        <p>
          <label id="optionEditTitle"></label>
        </p>
        <p><?=VALUE?>:</p>
        <p>
          <input type="text" id="optionEditValue" class="inputCombo">
        </p>
        <p>
          <button class="button" onclick="Admin.submitManageOption()">
            <?=EDIT_OPTION?>
          </button>
          <button class="button" onclick="gID('manageEditOptions').className += ' Invisible';">
            <?=CLEAR?>
          </button>
        </p>
      </form>
    </div>
  </div>
  <!--[/OPTIONS MANAGER]-->
  
</div>