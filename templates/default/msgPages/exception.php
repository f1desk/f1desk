<?php print("<? xml version='1.0' encoding='UTF-8' ?> \n"); ?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
    <head>
      <title>##ERROR_TITLE##</title>
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
      <div id="box" class="boxForm">
        <h2 style='text-align:center' id='StTitle'>##TITLE##</h2>
        <div>
          <p style='text-align:center'>
            <span class="TxMessage" id='StMessage'>##MESSAGE##</span>
          </p>
        </div>
      </div>
    </body>
  </html>