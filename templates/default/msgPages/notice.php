<?php print("<? xml version='1.0' encoding='UTF-8' ?> \n"); ?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
    <head>
      <title>Erro !</title>
      <style type="text/css">
        .boxForm {
          margin: 5px 5px 5px 0;
          padding: 5px;
          border: 1px solid #666;
          background: #DDD;
          font: 10px verdana, arial, sans-serif;
          text-align: center;
        }

        .error {
          border-color: red;
          color: red;
          background: #FDD;
        }

        .ok {
          border-color: green;
          color: green;
          background: #DFD;
        }

      </style>
    </head>
    <body>
      <div id="box" class="boxForm">
        <p style='text-align:center'>
          <span class="TxMessage" id='StMessage'>##MESSAGE##</span>
        </p>
      </div>
    </body>
  </html>