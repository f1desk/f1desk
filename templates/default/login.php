<?php
require_once('header.php');
?>
<div id="loginTemplate">
	<div id="loginBox">
		<div id="boxTitle">
			Efetue Login
		</div>
		<div id="boxContent">
			<form action='login.submit.php' method="POST">
			  <table>
			    <tr>
			      <td>Informe seu e-mail</td>
			    </tr>
			    <tr>
			      <td><input type='text' name='StEmail' /></td>
			    </tr>
			    <tr>
			    <tr>
			      <td>Informe sua senha</td>
			    </tr>
			      <td><input type="password"s" name="StPassword"></td>
			    </tr>
			    <tr>
			      <td><button class="button" type="submit" name='Enviar'>Enviar</button></td>
			    </tr>
			    <tr>
			      <td><input type="hidden" name="userKind" value="user"></td>
			    </tr>
			  </table>
			</form>
		</div><!-- BoxContent -->
	</div><!-- Login Box -->
</div>
<?php
require_once('footer.php');
?>