<?php
	session_start();		include_once( "./main.php" );
	$mensage = "";
	$ArThisClient = array(
		"ItID" => "",
		"StName" => "",
		"StEmail" => "",
		"TxSign" => "",
		"TxHeader" => "",
		"IDUnit" => ""
	);
	
	if ( isset($_POST['doAction']) ) {
		$StSQL = "
		SELECT 
			U.StName, U.StEmail, U.TxSign, U.TxHeader, C.IDClient
		FROM
			".DBPREFIX."User U
		WHERE
			IDUser = ". $_POST['ItUser'] ."
		";
		$db = new DBHandler();	$db->execSQL( $StSQL );
		$ArThisReturn = $db->getResult("String");
		if ( count($ArThisReturn) != 0 ) {
			$ArThisSupporter = array(
				"ItID" => $_POST['ItUser'],
				"StName" => $ArThisReturn[0][0],
				"StEmail" => $ArThisReturn[0][1],
				"TxSign" => $ArThisReturn[0][2],
				"TxHeader" => $ArThisReturn[0][3],
			);
		} else {
			$mensage = "No users found with this ID.";
		}
		
	}

?>

<html>
	<head>
		<title>
			Editing a user
		</title>
		<script type="text/javascript">
			function checkForm01(tForm){
				if (tForm.ItUser.value == "") {
					alert("You must fill a user to edit"); return false;
				}
			}
			function checkForm02(tForm) {
				if (tForm.StPassword.value != tForm.StPasswordConfirmation.value){
					alert("Passwords must be equals."); return false;
				}
			}
		</script>
	</head>
	
	<body>
		<fieldset>
			<legend> Select a Supporter to edit </legend>
			<form name="userSelect" method="POST" onsubmit="return checkForm01(this)">
				<input type="hidden" value="doAction" name="doAction">
				User ID to Edit:	<br />
				<input type="text" value="" name="ItUser">
				<input type="submit" value="Edit">
			</form>
			<span style="color:red;"><?=$mensage?></span>
		</fieldset>
		
		<fieldset>
			<legend> Editing a Supporter </legend>
			<form name="userEdit" method="POST" action="userEdit.submit.php" onsubmit="return checkForm02(this)">
				<input type="hidden" name="ItID" value="<?=$ArThisSupporter["ItID"]?>">
				<table>
					<tr>
						<td>Name</td>
						<td><input type="text" name="StName" value="<?=$ArThisSupporter["StName"]?>"></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><input type="text" name="StEmail" value="<?=$ArThisSupporter["StEmail"]?>"></td>
					</tr>
					<tr>
						<td>Sign</td>
						<td><textarea name="TxSign" rows="8" cols="50"><?=$ArThisSupporter["TxSign"]?></textarea></td>
					</tr>
					<tr>
						<td>Header</td>
						<td><textarea name="TxHeader" rows="8" cols="50"><?=$ArThisSupporter["TxHeader"]?></textarea></td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="password" name="StPassword"></td>
					</tr>
					<tr>
						<td>Password Confirm.</td>
						<td><input type="password" name="StPasswordConfirmation"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" value="Confirm Edition"></td>
					</tr>
				</table>
			</form>
		</fieldset>
	</body>
</html>