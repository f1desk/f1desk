<?php

#
# Padrao
#
require_once(dirname(__FILE__) . '/main.php');

Validate::ExternalUser();

Validate::Email( $_POST['StEmail'] );

$UserHandler = new UserHandler();
$MailHandler = new MailHandler();

$ArUserData = $UserHandler->getForeignUserData( $_POST['StEmail'] );

$ArUserData['StPassword'] = $UserHandler->generatePassword();

$Retorno = $UserHandler->insertUser( $ArUserData );

$MailHandler->setHTMLBody(false);
$StBody = str_replace('##PASSWORD##',$ArUserData['StPassword'],LNG_GETPASS_MSG);
$MailHandler->sendMail($_POST['StEmail'],LNG_GETPASS_SUBJ,$StBody);

header('Location: getPassword.php');

?>