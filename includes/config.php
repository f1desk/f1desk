<?php

define('MAILHOST','smtp.f1desk.org');
define('MAILUSER','contato=f1desk.org');
define('MAILPASS','f!2d2m');
define('MAILFROM','contato@f1desk.org');

define('ABSPATH','/helpdesk/');
define('APPDIR', '/var/www/' . ABSPATH); //PREENCHIDO NO MOMENTO DA INSTALACAO
define('CLASSDIR',APPDIR . 'class/');
define('INCLUDESDIR',APPDIR . 'includes/');
define('LANGDIR',APPDIR . 'lang/');
define('UPLOADDIR', 'uploads/');
define('UPLOAD_OPT','FTP');
define('UPLOAD_MAX_SIZE',2000000);
define('ABSTEMPLATEDIR',APPDIR . 'templates/' . getOption('template') . '/');

define('TEMPLATEDIR','templates/' . getOption('template') . '/');
define('JSDIR','js/');
define('CSSDIR','css/');


define('ISEXTERNAL',1);
?>