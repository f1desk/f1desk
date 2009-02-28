<?php
	session_start();		include_once( "./main.php" );
	$ArSupporters = F1deskUtils::listSupporters();
?>

<html>
	<head>
		<title>
			Deleting a User or a Supporter
		</title>
		
		<script>
			function confirmDelete(){
				if ( !confirm("This user will be removed! Are you sure? ") ) {
					return false;
				}
				return true;
			}
		</script>
	</head>
	
	<body>
		<fieldset>
			<legend>	Deleting a Supporter	</legend>
			<form method="POST" action="supporterDelete.submit.php" onsubmit="return confirmDelete();">
				<input type="hidden" name="doSupporterAction" value="doSupporterAction">
				<select name="IDUser">
					<option selected value="">-- Select a Supporter --</option>
					<?php
						foreach ( $ArSupporters as $ArSuppOpt )
							echo "<option value='". $ArSuppOpt['IDSupporter'] ."'>". $ArSuppOpt['StName'] ."</option>";
					?>
				</select>
				<input type="submit" value="Remove">
			</form>
		</fieldset>
	</body>
</html>