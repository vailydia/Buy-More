
<?php
session_start();
include_once ('lib/csrf.php');

if($_SESSION['t4210']){
	if($t = json_decode(stripslashes($_COOKIE['t4210']),true)) {
			if($t['em'] == "weiling@ierg4210.com"){
				header('Location: admin.php');
			  exit();
			}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Buy More Shop - Login Panel</title>
	<link href="mystyles.css" rel="stylesheet"/>

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

<section id="signUpPanel">
	<fieldset>
		<legend>Sign up</legend>
		<form id="signUpForm" method="POST" action="auth-process.php?action=<?php  echo ($action='signUp');  ?>">
			<label for="signUp_email">Email:</label>
			<div><input id="signUp_email" type="email" name="email" required="true" pattern="^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$" /></div>
      <label for="signUp_password">Password:</label>
      <div><input id="signUp_password" type="password" name="password" required="true" pattern="^[\w@#$%\^\&\*\-]+$" /></div>
      <input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
</section>

<h4 id="email">
	<?php
		 if(!empty($_COOKIE['t4210'])){
			 if($t = json_decode(stripslashes($_COOKIE['t4210']),true)) {
					 if($t['em']){
							echo $t['em'];
					 }
			 }
		 }else{
				echo "Guest";
		 }
	?>
</h4>

<section id="changePassword">
	<fieldset>
		<legend>Change your Password</legend>
		<form id="changePwForm" method="POST" action="auth-process.php?action=<?php  echo ($action='changePw');  ?>">
			<label for="email">Email:</label>
			<div><input id="signUp_email" type="email" name="email" required="true" pattern="^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$" /></div>
      <label for="password">Old Password:</label>
      <div><input id="old_password" type="password" name="oldPassword" required="true" pattern="^[\w@#$%\^\&\*\-]+$" /></div>
			<label for="password">New Password:</label>
      <div><input id="new_password" type="password" name="newPassword" required="true" pattern="^[\w@#$%\^\&\*\-]+$" /></div>
			<input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
</section>

<section id="orderInfo">
	<fieldset>
		<legend>Latest Transaction Records</legend>
		<ul id="ordersListOfUser">


		</ul>
  </fieldset>
</section>

<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript">
(function(){
	get = function(param, successCallback) {
		param = param || {};
		param.rnd =  new Date().getTime(); // to avoid caching in IE
		myLib.processJSON('checkout-process.php?' + encodeParam(param), null, successCallback);
	};

	encodeParam = function(obj) {
		var data = [];
		for (var key in obj)
			data.push(encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]));
		return data.join('&');
	};

	function updateOrdersList() {
	  var email = el('email').innerHTML.substr(2);
		var listItems = [];

		listItems.push('<li><span class="user">customer</span><span class="digest">digest</span><span class="salt">salt</span><span class="tid">tid</span></li>');

		get({action:'order_fetchall'}, function(json){
			for (var i = 0, order; order = json[i]; i++) {
				if(email == order.user && (email != "Guest")){
						listItems.push('<li id="order' , parseInt(order.oid) , '"><span class="user">' ,
						order.user, '</span><span class="digest">',order.digest , '</span><span class="salt">',order.salt ,
						 '</span><span class="tid">',order.paid ,'</span></li>');
				}
			}
			el('ordersListOfUser').innerHTML = listItems.join('');

		});

	}

	updateOrdersList();


})();
</script>

</body>
</html>
