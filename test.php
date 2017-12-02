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

echo "<br/>";
echo "<br/>";
echo "<br/>";


//list	{"2":1,"4":1,"5":1}

$listString = "2,1,4,1,";

$list = explode(',', $listString);
echo "<br/>";
echo count($list);
for($i=0,$index=0; $i<count($list); $i+=2,$index++){
  echo "list:<br/>";
  echo $list[$i];
  echo "<br/>";
  echo $list[$i+1];
}
$listOfPid=array();
$listOfQuantity=array();
for ($i=0,$index=0; $i<count($list)-1; $i+=2,$index++){
  $listOfPid[$index]=$list[$i];
  $listOfQuantity[$index]=$list[$i+1];
}
echo "<br/>";
echo $listOfPid;
echo $listOfQuantity;

for($i=0,$index=0; $i<count($listOfPid); $i+=1,$index++){
  echo "<br/>listOfPid:";
  echo $listOfPid[$i];
  echo "<br/>listOfQuantity:";
  echo $listOfQuantity[$i];
  echo "<br/>";
}

global $db;
$db = ierg4210_DB();

//get the total price
$sumPrice = 0;
for($i = 0; $i<count($listOfPid); $i++) {

    $pid = (int)$listOfPid[$i];
    echo "<br/>pid:";
    echo $pid;
    $q = $db->prepare("SELECT * FROM products WHERE pid = $pid;");
    if ($q->execute()) {
        $product = $q->fetchAll();
    }


echo "<br/>price:";
echo (int)$product[0]["price"];
    $sumPrice += (int)$product[0]["price"] * (int)$listOfQuantity[$i];
}

echo "<br/>";
echo "testing-------";
echo "<br/>sum = ";
echo $sumPrice;


error_reporting(E_ALL ^ E_NOTICE);

error_log("ipn Listener start");

?>
