
<?php

session_start();
include_once ('lib/db.inc.php');
//
// if (empty($_SESSION['t4210'])){
// 	header('Location: login.php');
// 	exit();
// }

function ierg4210_handle_checkout() {

  $listString = $_REQUEST['list'];

  $list = explode(',', $listString);
  $listOfPid=array();
  $listOfQuantity=array();
  for ($i=0,$index=0; $i<count($list)-1; $i+=2,$index++){
    $listOfPid[$index]=$list[$i];
    $listOfQuantity[$index]=$list[$i+1];
  }

  global $db;
  $db = ierg4210_DB();

  //get the total price
  $sumPrice = 0;
  for($i = 0; $i<count($listOfPid); $i++) {

      $pid = (int)$listOfPid[$i];
      $q = $db->prepare("SELECT * FROM products WHERE pid = $pid;");
      if ($q->execute()) {
          $product = $q->fetchAll();
      }

      $sumPrice += (int)$product[0]["price"] * (int)$listOfQuantity[$i];
  }


  // generate a digest and store it with the random salt into database

  $Currency="HKD";
	$MerchantEmail = "vailydia-facilitator@hotmail.com";
	$salt = mt_rand() . mt_rand();
  //$digest = hash_hmac('sha1', $Currency. $MerEmail. $salt. $list. $sumPrice, $salt);
  $digest = hash_hmac('sha1', $Currency. $MerchantEmail. $salt, $salt);

  $q = $db->prepare("INSERT INTO orders (user, digest, salt, paid) VALUES (?, ?, ?, ?)");
  $userEmail;
  if($_SESSION['t4210']['em']){
    $userEmail=$_SESSION['t4210']['em'];
  }else{
    $userEmail='Guest';
  }
  $q->execute(array($userEmail, $digest, $salt, "notyet")); //insert digest
  $invoice=$db->lastInsertId();

  $returnValue=array("digest"=>$digest, "invoice"=>$invoice, "amount"=>$sumPrice);

  return $returnValue;
}


function ierg4210_order_fetchall() {
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM orders ORDER BY oid DESC LIMIT 50;");
	if ($q->execute())
		return $q->fetchAll();
}




header('Content-Type: application/json');

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

try {

	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode())
			error_log(print_r($db->errorInfo(), true));
		echo json_encode(array('failed'=>'1'));
	}
	echo 'while(1);' . json_encode(array('success' => $returnVal));
} catch(PDOException $e) {
	error_log($e->getMessage(),0);
	echo json_encode(array('failed'=>'error-db'));
} catch(Exception $e) {
	echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
}


?>
