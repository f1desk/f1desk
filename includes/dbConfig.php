<?php

define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','mario');
define('DBNAME','helpdesk');
define('DBPREFIX', 'f1desk_');


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