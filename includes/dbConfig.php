<?php

define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','mario');
define('DBNAME','helpdesk');
define('DBPREFIX', 'f1desk_');

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