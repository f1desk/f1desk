<?php require_once(dirname(__FILE__).'/../../../adminData.php');?>
<div id='adminWrapper'>
  
  <!--[OPTIONS MANAGER]-->
  <?=ErrorHandler::getNotice('option');?>
  <div id="content1" style="width:90%;">
    <div id='manageOptions' class='Left'>
      <h3>Gerenciar Preferências</h3>
      <table class='tableTickets'>
        <thead>
          <th>Op&ccedil;&otilde;es</th>
          <th>Valores</th>
          <th>A&ccedil;&otilde;es</th>
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
      <h3>Editar Opção</h3>
      <form onsubmit="return false;">
        <p>Opção:</p>
        <p>
          <label id="optionEditTitle"></label>
        </p>
        <p>Valor:</p>
        <p>
          <input type="text" id="optionEditValue" class="inputCombo">
        </p>
        <p>
          <button class="button" onclick="Admin.submitManageOption()">
            Editar
          </button>
          <button class="button" onclick="gID('manageEditOptions').className += ' Invisible';">
            Limpar
          </button>
        </p>
      </form>
    </div>
  </div>
  <!--[/OPTIONS MANAGER]-->
  
  <!--[LANGUAGE MANAGER]-->
  <?=ErrorHandler::getNotice('language');?>
  <div id="content2" style="width:90%;padding-top:215px;">
    <div id='manageLanguages' class='Left'>
      <h3>Gerenciar Línguas</h3>
      <table class='tableTickets'>
        <thead>
          <th>T&iacute;tulo</th>
          <th>Diret&oacute;rio</th>
          <th>A&ccedil;&otilde;es</th>
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
                <a href="javascript:void(0);" onclick="">
                  <img src="<?=TEMPLATEDIR?>images/button_edit.png"/>
                </a>
                <?if (!$ArLanguageOptions['BoSelected']):?>
                  <a href="javascript:void(0);" onclick="">
                    <img src="<?=TEMPLATEDIR?>images/unignore.png"/>
                  </a>
                <?endif;?>
              </td>
            </tr>
          <? endforeach; ?>
        </tbody>
      </table>
    </div>
    <div id="manageEditLanguages" class="Left">
      <h3>Editar Língua</h3>
      <form onsubmit="return false;">
        <p>Título:</p>
        <p>
          <input type="text" id="languageEditTitle" class="inputCombo">
        </p>
        <p>Diretório:</p>
        <p>
          <input type="text" id="languageEditDir" class="inputCombo">
        </p>
        <p>
          <button class="button" onclick="">
            Editar
          </button>
          <button class="button" onclick="gID('manageEditLanguages').className += ' Invisible';">
            Limpar
          </button>
        </p>
      </form>
    </div>
  </div>
  <!--[/LANGUAGE MANAGER]-->
  
</div>