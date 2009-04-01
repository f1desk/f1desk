<?php
	require_once(dirname(__FILE__) . '/main.php');

	$ArData = array();
	#
	# We need to know what must be updated, otherwise, data already saved will be deleted
	#
	if (array_key_exists( 'StName', $_POST )) {
		$ArData['StName'] = f1desk_escape_string($_POST['StName']);
	}

  if (array_key_exists('StPassword',$_POST) && ! empty($_POST['StPassword'])) {
    $ArData['StPassword'] = $_POST['StPassword'];
  }

	if (array_key_exists( 'StEmail', $_POST )) {
		$ArData['StEmail'] = f1desk_escape_string($_POST['StEmail']);
	}

	if (array_key_exists( 'BoNotify', $_POST )) {
		$ArData['BoNotify'] = $_POST['BoNotify'];
	}

	if (array_key_exists( 'TxHeader', $_POST )) {
		$ArData['TxHeader'] = f1desk_escape_string($_POST['TxHeader']);
	}

	if (array_key_exists( 'TxSign', $_POST )) {
		$ArData['TxSign'] = f1desk_escape_string($_POST['TxSign']);
	}

	$UserHanlder = new UserHandler();
	$ItAffedcted = $UserHanlder->updateUser($ArData, getSessionProp('IDUser'));

	returnData($_POST['returnType'],$_POST['returnUrl']);
?>