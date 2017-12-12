<?php

session_start();
include_once ('lib/csrf.php');
include_once ('lib/db.inc.php');



function ierg4210_login() {

    if ( empty($_POST['email']) || empty($_POST['password'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/",$_POST['email'])
    || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['password']))
    {
      throw new Exception('Please input appropriate email or password');
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
        header('Refresh: 1; url=login.php');
      	echo '<strong>You have no permission to admin panel.</strong> <br/>Redirecting to login page in 2 seconds...';
        //throw new Exception('Wrong Credentials');
    }

}



function ierg4210_signUp() {

    if ( empty($_POST['email']) || empty($_POST['password'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/",$_POST['email'])
    || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['password']))
    {
      throw new Exception('Please input appropriate email or password');
      header('Refresh:1; url=login.php');
      exit();
    }

    //new users
    global $db;
    $db = ierg4210_DB();
    $email = $_POST['email'];
    $salt = mt_rand();
    $password = $_POST['password'];


    $salted_password = hash_hmac('sha1',$password, $salt);

    $q = $db->prepare("INSERT INTO users (email,salt,password,flag) VALUES (?, ?, ?, ?)");
    if($q->execute(array($email,$salt,$salted_password,0))){
        ierg4210_login();
    }

    header('Location:index.php');
    exit();
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


function ierg4210_changePw() {
    global $db;
    $db = ierg4210_DB();
    $email = $_POST['email'];
    $oldPw = $_POST['oldPassword'];
    $newPw = $_POST['newPassword'];

    $q = $db->prepare("SELECT * FROM users WHERE email = ?");
    $q->execute(array($email));

    if($r = $q->fetch()){

        $saltPassword = hash_hmac('sha1', $oldPw, $r['salt']);
        if($saltPassword == $r['password']){

            $salt = mt_rand();
            $salted_password = hash_hmac('sha1',$newPw, $salt);

            $k = $db->prepare("UPDATE users SET salt=?,password=? WHERE email = ?;");
            if($k->execute(array($salt,$salted_password,$email))){
                ierg4210_logout();
            }

        }else{
            alert('old password is wrong.');
            exit();
        }

     }else{
         alert('user does not exist: ' + $email);
         exit();
     }

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

        setcookie('t4210', json_encode($token), $exp, '','',true,true);
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

          setcookie('t4210', json_encode($token), $exp, '','',true,true);
          $_SESSION['t4210'] = $token;
          return true;
      }
      return false;
  }

	return false;
}





header("Content-type: text/html; charset=utf-8");


try {

    //input validation
    if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
        throw new Exception('Undefined Action');
    }

    //check if the form request can present a valid nonce
    if($_REQUEST['action'] == 'login' || $_REQUEST['action'] == 'logout' || $_REQUEST['action'] == 'signUp' || $_REQUEST['action'] == 'changePw') {
        csrf_verifyNonce($_REQUEST['action'],$_POST['nonce']);
    }

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
