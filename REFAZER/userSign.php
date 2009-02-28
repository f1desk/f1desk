<?php
	session_start();		include_once( "./main.php" );
	$ArUnits = F1DeskUtils::listUnits();
	$ArDeptos = TemplateHandler::getDepartments();
	//die('<pre>' . print_r( $ArDeptos,1 ));
?>

<html>
	<head>
		<title>
			Cadastro de Usu&aacute;rio
		</title>
		
		<script type="text/javascript">
			function checkForm(tForm){
				for(var aux = 0; aux < tForm.length; aux++){
					if( tForm[aux].value == "" ){
						alert("Please, fill all fields."); return false;
					}
				}
				var er = /^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/;
				if (er.test(tForm['StEmail'].value) == false){
					alert("Email invalido");	return false;
				}
				if (tForm['StPassword'].value != tForm['StPasswordConfirmation'].value){
					alert("As senhas nao conferem"); tForm['StPassword'].focus(); return false;
				}
			}
		</script>
	</head>
	
	<body>
	
		<!--  Users insert -->
		<form name="f1UserSign" method="POST" action="userSign.submit.php" onsubmit="return checkForm(this);">
			<fieldset>
				<legend>Insert a Client</legend>
				<table width="500">
					<tr>
						<td>Name</td>
						<td><input type="text" name="StName"></td>
					</tr>
					<tr>
						<td>E-mail</td>
						<td><input type="text" name="StEmail"></td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="password" name="StPassword"></td>
					</tr>
					<tr>
						<td>Password Confirmation</td>
						<td><input type="password" name="StPasswordConfirmation"></td>
					</tr>
					<tr>
						<td><input type="submit" value="Sign In"></td>
					</tr>
				</table>
			</fieldset>
		</form>
		
		
		<!--  Supporters insert -->
		<form name="f1UserSign" method="POST" action="supporterSign.submit.php" onsubmit="return checkForm(this);">
			<fieldset>
				<legend>Insert a Supporter</legend>
				<table width="500">
					<tr>
						<td>Name</td>
						<td><input type="text" name="StName"></td>
					</tr>
					<tr>
						<td>E-mail</td>
						<td><input type="text" name="StEmail"></td>
					</tr>
					<tr>
						<td>Unit</td>
						<td>
							<select name="ItUnit">
								<option selected value="">-- Select Unit --</option>>
								<?php
									foreach ( $ArUnits as $IDUnit => $StUnit ){
										echo "<option value='". $IDUnit ."'>". $StUnit ."</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Department</td>
						<td>
							<select name="ItDepto">
								<option selected value="">-- Select Department --</option>
								<?php
									foreach ( $ArDeptos as $ItID => $ArDeptOpt ){
										echo "<option value='". $ItID ."'>". $ArDeptOpt[ 'StName' ] ."</option>";
										if ( count($ArDeptOpt['SubDepartment']) != 0 ) {
											foreach ($ArDeptOpt['SubDepartment'] as $ItSubDeptID => $ArSubDeptOpt) {
												echo "<option value='". $ItSubDeptID . "'>&nbsp;&nbsp;&nbsp;" . $ArSubDeptOpt['StName'] . "</option>";
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
						<td>Password Confirmation</td>
						<td><input type="password" name="StPasswordConfirmation"></td>
					</tr>
					<tr>
						<td><input type="submit" value="Sign In"></td> 
					</tr>
				</table>
			</fieldset>
		</form>
		
	</body>
</html>