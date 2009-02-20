<?php

define('MAILHOST','smtp.f1desk.org');
define('MAILUSER','contato=f1desk.org');
define('MAILPASS','f!2d2m');
define('MAILFROM','contato@f1desk.org');

define('ABSPATH','www/helpdesk/');
define('APPDIR', '/home/f1desk/' . ABSPATH); //PREENCHIDO NO MOMENTO DA INSTALACAO
define('CLASSDIR',APPDIR . 'class/');
define('INCLUDESDIR',APPDIR . 'includes/');
define('LANGDIR',APPDIR . 'lang/');
define('UPLOADDIR',APPDIR . 'uploads/');
define('ABSTEMPLATEDIR',APPDIR . 'templates/' . getOption('template') . '/');

define('TEMPLATEDIR','templates/' . getOption('template') . '/');
define('JSDIR','js/');


define('ISEXTERNAL',1);
?>