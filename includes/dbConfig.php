<?php

define('DBHOST','mysql.f1desk.org');
define('DBUSER','f1desk2');
define('DBPASS','f!2d2m');
define('DBNAME','f1desk2');
define('DBPREFIX', '');

define('USERDBHOST','mysql1.dimitrilameri.com');
define('USERDBUSER','dimitrilameri1');
define('USERDBPASS','180988');
define('USERDBNAME','dimitrilameri1');

#
# Este campo devera ser preenchido com o prefixo no momento de gerar o config ( caso este nao seja importacao )
#
define('USERDBTABLE','usuario');

$UserFields = array(
  'IDExternalUser'=>'id_usuario',
  'StName'=>'nome',
  'StEmail'=>'email'
);

?>