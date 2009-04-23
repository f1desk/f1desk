<?php 
  require_once(dirname(__FILE__).'/../../../adminData.php');
  handleLanguage(__FILE__);
?>
<div id='adminWrapper'>
  <?=ErrorHandler::getNotice('template');?>
  <div id="manageTemplates">
    <h3><?=ADD_TEMPLATE?></h3>
    <form onsubmit="return false;" id="templateForm">
        <?=TEMPLATE_NAME?>:
      <p>
        <input type="text" name="StName" class="inputCombo">
      </p>
        <?=TEMPLATE_PATH?>:
      <p>
        <input type="text" name="StPath" class="inputCombo">
      </p>
        <?=THUMBNAIL?>:
      <p>
        <input type="text" name="StThumbnail" class="inputCombo">
      </p>
        <?=DESCRIPTION?>:
      <p>
        <textarea class="answerArea" name="StDescription" style="width:40%; height:120px;"></textarea>
      </p>
      <p>
        <button class="button" onclick="Admin.submitManageTemplate();"><?=ADD_TEMPLATE?></button>
      </p>
    </form>
  </div>
  <div>
    <h3><?=ADDED_TEMPLATES?></h3>
    <?foreach ($ArTemplates as $ArTemplateOptions):?>
      <table class="tableTemplates">
        <tbody>
          <tr align="center">
            <td align="center" colspan="1" rowspan="6">
              <a href="javascript:void(0)" onclick="flowWindow.previewTemplate('templates/<?=$ArTemplateOptions['StPath']?>/<?=$ArTemplateOptions['StThumbnail']?>')">
                <img class="thumbnail" src="templates/<?=$ArTemplateOptions['StPath']?>/<?=$ArTemplateOptions['StThumbnail']?>">
              </a>
            </td>
            <span><?=strtoupper($ArTemplateOptions['StName'])?></span>
          </tr>
          <tr>
            <td><?=$ArTemplateOptions['StDescription']?></td>
          </tr>
          <tr>
            <td align="center">
              <?if (!$ArTemplateOptions['BoSelected']):?>
                <button class="button" onclick="Admin.setCurrentTemplate('<?=$ArTemplateOptions['StName']?>')"><?=USE_THIS?></button>
                <button class="button" onclick="Admin.deleteTemplate('<?=$ArTemplateOptions['StName']?>')"><?=DELETE_THIS?></button>
              <?else:?>
                <img src="<?=TEMPLATEDIR?>images/unignore.png"> <?=USING_THIS?>
              <?endif;?>
            </td>
          </tr>
        </tbody>
      </table>
    <?endforeach;?>
  </div>
</div>