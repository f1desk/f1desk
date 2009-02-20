<?php
	session_start();		include_once( "./main.php" );
	$ArSupporters = F1DeskUtils::listSupporters();
	$ArUnits = F1DeskUtils::listUnits();
	$ArDepartments = F1DeskUtils::listDepartments();
										//ErrorHandler::debug($ArUnits);
	$ArThisSupporter = array(
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
	U.StName, U.StEmail, U.TxSign, U.TxHeader, S.IDUnit, DS.IDDepartment
FROM DepartmentSupporter DS
	LEFT JOIN	Supporter S
		on(DS.IDSupporter = S.IDSupporter)
	LEFT JOIN User U
		on(S.IDUser = U.IDUser)
WHERE
	S.IDSupporter = ". $_POST['ItUser'] ;
		$db = new DBHandler();	$db->execSQL( $StSQL );
		$ArThisReturn = $db->getResult("String");
		//ErrorHandler::debug($ArThisReturn);
		$ArThisSupporter = array(
			"ItID" => $_POST['ItUser'],
			"StName" => $ArThisReturn[0][0],
			"StEmail" => $ArThisReturn[0][1],
			"TxSign" => $ArThisReturn[0][2],
			"TxHeader" => $ArThisReturn[0][3],
			"IDUnit" => $ArThisReturn[0][4],
			"IDDepartment" => $ArThisReturn[0][5]
		);
	}

?>

<html>
	<head>
		<title>
			Editing a Supporter
		</title>
		<script type="text/javascript">
			function checkForm01(tForm){
				if (tForm.ItUser.value == "") {
					alert("You must fill a supporter to edit"); return false;
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
				<select name="ItUser">
					<option selected value="">-- Select a Supporter --</option>
					<?php
						foreach ( $ArSupporters as $ArSuppOpt )
							echo "<option value='". $ArSuppOpt['IDSupporter'] ."'>". $ArSuppOpt['StName'] ."</option>";
					?>
				</select>
				<input type="submit" value="Edit">
			</form>
		</fieldset>
		
		<fieldset>
			<legend> Editing a Supporter </legend>
			<form name="userEdit" method="POST" action="supporterEdit.submit.php" onsubmit="return checkForm02(this)">
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
						<td>Unit</td>
						<td>
							<select name="IDUnit">
								<option selected value="">-- Select Unit --</option>
								<?php
									foreach ($ArUnits as $ItID => $StUnit){
										if ( isset($ArThisSupporter["IDUnit"]) && $ItID == $ArThisSupporter["IDUnit"] ) 	$selected = "selected";
										else  $selected = "";
										echo "<option $selected value='". $ItID ."'> ". $StUnit ." </option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Department</td>
						<td>
							<select name="IDDepto">
								<option selected value="">-- Select Department --</option>
								<?php
									foreach ( $ArDepartments as $ItID => $ArDeptOpt ){
										if ( isset($ArThisSupporter["IDDepartment"]) && $ItID == $ArThisSupporter["IDDepartment"] ) 	$selected = "selected";
										else  $selected = "";
										echo "<option value='". $ItID ."' $selected> * ". $ArDeptOpt[ 'StName' ] ."</option>";
										if ( count($ArDeptOpt['SubDepartment']) != 0 ) {
											foreach ($ArDeptOpt['SubDepartment'] as $ItSubDeptID => $ArSubDeptOpt) {
												if ( isset($ArThisSupporter["IDDepartment"]) && $ItSubDeptID == $ArThisSupporter["IDDepartment"] ) 	$selected = "selected";
												else  $selected = "";
												echo "<option value='". $ItSubDeptID . "' $selected>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $ArSubDeptOpt['StName'] . "</option>";
											}
										}
									}
								?>
							</select>
						</td>
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