
<?php
session_start();
include_once ('lib/csrf.php');

if ($_SESSION['t4210']){
    //avoid repeat login
	header('Location: admin.php');
	exit();
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Buy More Shop - Login Panel</title>
</head>

<body>

<h3><a href="/index.php">Back to Home</a></h3>

<h1>Buy More Shop- Login Panel</h1>

<section id="loginPanel">
	<fieldset>
		<legend>Login</legend>
		<form id="loginForm" method="POST" action="auth-process.php?action=<?php  echo ($action='login');  ?>">
			<label for="login_email">Email:</label>
			<div><input id="login_email" type="email" name="email" required="true" pattern="^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$" /></div>
            <label for="login_password">Password:</label>
            <div><input id="login_password" type="password" name="password" required="true" pattern="^[\w@#$%\^\&\*\-]+$" /></div>
            <input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
</section>


</body>
</html>
