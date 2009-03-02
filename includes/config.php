<?php

define('MAILHOST','smtp.dimitrilameri.com');
define('MAILUSER','contato=dimitrilameri.com');
define('MAILPASS','f!2d2m');
define('MAILFROM','contato@dimitrilameri.com');

define('ABSPATH','www/helpdesk/');
define('APPDIR', '/var/' . ABSPATH); //PREENCHIDO NO MOMENTO DA INSTALACAO
define('CLASSDIR',APPDIR . 'class/');
define('INCLUDESDIR',APPDIR . 'includes/');
define('LANGDIR',APPDIR . 'lang/');
define('UPLOADDIR',APPDIR . 'uploads/');
define('UPLOAD_OPT','FTP');
define('UPLOAD_MAX_SIZE',2000000);
define('ABSTEMPLATEDIR',APPDIR . 'templates/' . getOption('template') . '/');

define('TEMPLATEDIR','templates/' . getOption('template') . '/');
define('JSDIR','js/');


define('ISEXTERNAL',1);
?>