<?php
include_once('lib/db.inc.php');

$salt = mt_rand();
$salted_password = hash_hmac('sha1',"qwert", $salt);

echo "qwert";
echo "<br/>";
echo $salt;
echo "<br/>";
echo $salted_password;


echo "<br/>";
echo "<br/>";
session_start();
echo "SESSION:";
echo $_SESSION['t4210'];
echo "<br/>";
if(!empty($_SESSION['t4210'])){
    $db = ierg4210_DB();
    $q = $db->prepare('SELECT * FROM users WHERE email = ?');
    echo "<br/>";
    echo $_SESSION['t4210']['em'];
    $q -> execute(array($_SESSION['t4210']['em']));
    if($r = $q -> fetch()){
        if($r['flag'] == 0){
           echo "<br/>";
           echo "this is first return...";
           return false;
        }
        $realk=hash_hmac('sha1', $_SESSION['t4210']['exp'].$r['password'], $r['salt']);
        if($realk == $_SESSION['t4210']['k']){
          echo "<br/>";
          echo "this is second return...";
            return $_SESSION['t4210']['em'];
        }
    }
    echo "<br/>";
    echo "this is third if...";
    return false;
}



echo "<br/>";
echo $_COOKIE['t4210'];



?>
