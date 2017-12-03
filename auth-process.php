<?php

session_start();
include_once ('lib/csrf.php');
include_once ('lib/db.inc.php');



function ierg4210_login() {

    if ( empty($_POST['email']) || empty($_POST['password'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/",$_POST['email'])
    || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['password']))
    {
      throw new Exception('Wrong Credentials');
    }

    //Implement the login logic here
    $login_success = loginProcess($_POST['email'],$_POST['password']);

    if ($login_success) {
        //prevent session fixation
		    session_regenerate_id(true);

        //redirect to admin page
        header('Location:admin.php', true, 302);
        exit();
    } else {
        //header('Location:index.php', true, 302);
        header('Refresh: 2; url=index.php');
      	echo '<strong>You have no permission.</strong> <br/>Redirecting to index page in 2 seconds...';
        //throw new Exception('Wrong Credentials');
    }

}




function ierg4210_logout() {
    //clear the cookies and session
  	setcookie('t4210','',time()-3600);
  	$_SESSION['t4210']=null;
    echo '<strong>You have logged out successfully.</strong> <br/>';
    //redirect to login page after logout
    header('Location:login.php');
    exit();

}




function loginProcess($email, $password){

  global $db;
	$db = ierg4210_DB();
  $q = $db->prepare("SELECT * FROM users WHERE email = ?");

  $q->execute(array($email));

  if($r = $q->fetch()){

      $saltPassword = hash_hmac('sha1', $password, $r['salt']);

      if($r['flag'] == 0 && $saltPassword == $r['password']){

        $exp = time() + 3600 * 24 * 3;
        //$token = array( 'em'=>$r['email'], 'exp'=>$exp,'k'=>sha1($exp . $r['salt'] . $r['password']));
        $token = array(
            'em' => $r['email'],
            'exp' => $exp,
            'k' => hash_hmac('sha1', $exp.$r['password'], $r['salt']));

        setcookie('t4210', json_encode($token), $exp, '','',false,true);
        $_SESSION['t4210'] = $token;

        return false;

      }

      if($r['flag'] == 1 && $r['email'] == 'weiling@ierg4210.com' && $saltPassword == $r['password']) {
          $exp = time() + 3600 * 24 * 3;
          //$token = array( 'em'=>$r['email'], 'exp'=>$exp,'k'=>sha1($exp . $r['salt'] . $r['password']));
          $token = array(
              'em' => $r['email'],
              'exp' => $exp,
              'k' => hash_hmac('sha1', $exp.$r['password'], $r['salt']));

          setcookie('t4210', json_encode($token), $exp, '','',false,true);
          $_SESSION['t4210'] = $token;
          return true;
      }
      return false;
  }
  echo '<script language="javascript">';
  echo 'alert("Invalid name or password")';
  echo '</script>';

  //else: new users
  //
  // $q = $db->prepare("INSERT INTO users (email,salt,password,flag) VALUES (?,?,?,?)");
  // $salt = mt_rand();
  // $salted_password = hash_hmac('sha1',$password, $salt);
  //
  // $q->execute(array($email,$salt,$salted_password,0));

	return false;
}





header("Content-type: text/html; charset=utf-8");


try {

    //input validation
    if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
        throw new Exception('Undefined Action');
    }

    //check if the form request can present a valid nonce
    if($_REQUEST['action'] == 'login') {
        error_log("begin to verify nonce:" . $_POST['nonce']);
        csrf_verifyNonce($_REQUEST['action'],$_POST['nonce']);
    }


    // run the corresponding function according to action
	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode())
			error_log(print_r($db->errorInfo(), true));
    throw new Exception('Failed');
	}

} catch(PDOException $e) {
	error_log($e->getMessage());
	header('Refresh: 3; url=login.php?error=db');
	echo '<strong>Error Occurred:</strong> DB <br/>Redirecting to login page in 3 seconds...';
} catch(Exception $e) {
	header('Refresh: 3; url=login.php?error=' . $e->getMessage());
	echo '<strong>Error Occurred:</strong> ' . $e->getMessage() . '<br/>Redirecting to login page in 3 seconds...';
}



?>
