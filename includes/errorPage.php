<?php

function getErrorPageHTML($StType,$StTitle,$StMessage,$ItSeverity,$StFile,$ItLine) {
  $Html = <<<EOF_HTML
<?xml version="1.0" encoding="UTF-8"?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
    <head>
      <title>Erro !</title>
      <style type="text/css">
        .boxForm {
          background-color:#F4F8FE;
          border:1px solid #BFD7E3;
          margin:2px;
          padding:5px;
          width:95%;
          margin-left:26px;
          margin-top:13px;
          text-align:left;
        }
        .TxMessage {
          color:#101E39;
          font-size:13px;
          font-weight:bold;
        }
        .TxTitle {
          color:#101E39;
          font-size:13px;
        }
      </style>
    </head>
    <body>
      <div id="erro" class="boxForm">
        <h2 style='text-align:center'>$StTitle</h2>
EOF_HTML;


  if ($StType == 'ERR') {
    $Html .= <<<EOF_HTML

          <div>
            <ul style='list-style-type: none; padding-left:10px;'>
              <li>
                <span class='TxTitle'>Mensagem :</span>
                <span class="TxMessage" >$StMessage</span>
              </li>
              <li>
                <span class='TxTitle'>Severidade :</span>
                <span class="TxMessage">$ItSeverity</span>
              </li>
              <li>
                <span class='TxTitle'>Arquivo :</span>
                <span class="TxMessage">$StFile</span>
              </li>
              <li>
                <span class='TxTitle'>Linha :</span>
                <span class="TxMessage">$ItLine</span>
              </li>
            </ul>
          </div>
EOF_HTML;
  } else {
    $Html .= <<<EOF_HTML

          <p style='text-align:center'>
            <span class="TxMessage">$StMessage</span>
          </p>
EOF_HTML;
  }
  $Html .= <<<EOF_HTML

      </div>
    </body>
  </html>
EOF_HTML;

  return $Html;
}

?>