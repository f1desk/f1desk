<?php

define('MAILHOST','smtp.dimitrilameri.com');
define('MAILUSER','contato=dimitrilameri.com');
define('MAILPASS','f!2d2m');
define('MAILFROM','contato@dimitrilameri.com');

define('ABSPATH','www/helpdesk/');
define('APPDIR', 'C:/' . ABSPATH); //PREENCHIDO NO MOMENTO DA INSTALACAO
define('CLASSDIR',APPDIR . 'class/');
define('INCLUDESDIR',APPDIR . 'includes/');
define('LANGDIR',APPDIR . 'lang/');
define('UPLOADDIR',APPDIR . 'uploads/');

define('ABSTEMPLATEDIR',APPDIR . 'templates/' . getOption('template') . '/');
define('TEMPLATEDIR','templates/' . getOption('template') . '/');
define('PAGEDIR',TEMPLATEDIR . 'msgPages/');
define('ABSPAGEDIR',ABSTEMPLATEDIR . 'msgPages/');

define('JSDIR','js/');
define('CSSDIR','css/');

define('ISEXTERNAL',0);
?>