<?php

include_once ('lib/db.inc.php');
error_reporting(E_ALL ^ E_NOTICE);

	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
		$req .= "&$key=$value";
	}

	error_log("req is: " .$req);

	// post back to PayPal system to validate
	$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Host: www.paypal.com\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n";
	$header .= "Connection: close\r\n\r\n";

  //open a socket to PayPal site
	$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

	if (!$fp) {
		// HTTP ERROR
		error_log("HTTP ERROR");

	} else { // NO HTTP ERROR

    //handshake, first, to notify the paypal
		fputs($fp, $header . $req);
		while (!feof($fp)) {

		  $res = fgets ($fp, 1024);
		  if (strcmp($res, "VERIFIED\r\n") == 0) { //compare, is VERIFIED true;

        // Check the payment_status is Completed
    		error_log($_POST['payment_status']);
    		if (empty($_POST['payment_status'])||$_POST['payment_status']!='Completed')
    		{
    			error_log("payment is not completed");
    			break;
    		}

        // Check unique txnid
        $valid_txnid = check_txnid($_POST['txn_id']);

        // Check correct receiver_email
        $valid_email = check_email($_POST['receiver_email']);

        // Check digest
				$valid_digest = check_digest();


        // PAYMENT VALIDATED & VERIFIED!
        // If 'VERIFIED', update the order database record(notyet -> transactions id);
				if ($valid_txnid && $valid_email && $valid_digest) {

					$comleted = updateOrders($_POST['txn_id']);
					if ($comleted) {
						error_log("record has been updated & successfully inserted into the Database");
					} else {
            error_log(" Error inserting into DB");
						// E-mail admin or alert user
						// mail('user@domain.com', 'PAYPAL POST - INSERT INTO DB WENT WRONG', print_r($data, true));
					}
				} else {
          error_log("Payment made but data has been changed");
					// E-mail admin or alert user
				}

			} else if (strcmp ($res, "INVALID") == 0) {
        // PAYMENT INVALID & INVESTIGATE MANUALY!
				// E-mail admin or alert user

        // If 'INVALID', TODO: Log for manual investigation.
        error_log('Live-INVALID IPN.'.'\n\n'.$req);
        exit();
			} else {
						error_log("NO invalid or VERIFIED find....");
			}
		}
	fclose ($fp);

	}









  //functions area
  function check_txnid($tnxid) {

      $db = ierg4210_DB();
      $valid_txnid = true;
      $q = $db->prepare("SELECT * FROM orders;");
      if ($q->execute()) {
          $cartOrders = $q->fetchAll();
      }
      $invoice = $_POST['invoice'];
      error_log("test invoice: ".$invoice);
      foreach($cartOrders as $order) {
        if ($order['oid'] == $invoice) {
          if ($order['paid'] == $tnxid) { //colomn 'paid' in order database
             error_log("Duplicate Traction!!!");
             $valid_txnid = false;;
          }
        }
      }

      return $valid_txnid;
  }


  function check_email($receiver_email) {
      $email = 'vailydia-facilitator@hotmail.com';
      $valid_email = true;
      if($_POST['receiver_email'] == $email){
  			error_log("correct email");
  		}else{
  			error_log("incorect email");
  			$valid_email = false;
  		}
      return $valid_email;
  }

  function check_digest() {
		  $valid_digest = false;

			$db = ierg4210_DB();
			$q = $db->prepare("SELECT * FROM orders WHERE oid = ?");
			if ($q->execute(array($_POST['invoice']))) {
				  $orders=$q->fetchAll();
			}

			$digestInDB=$orders[0]["digest"];
			$salt=$orders[0]["salt"];

			$Currency = $_POST['mc_currency'];
			$MerEmail = $_POST['business'];

      $digest = hash_hmac('sha1', $Currency. $MerEmail. $salt, $salt);

			if($digest == $digestInDB){
				  $valid_digest = true;
			}else{
				  error_log("Digest does not match!");
				  $valid_digest = false;
			}

      return $valid_digest;
  }

  function updateOrders($txn_id) {

      $db = ierg4210_DB();
      $q = $db->prepare("UPDATE orders SET paid = ? WHERE oid = ?;");
      return ($q->execute(array($_POST['txn_id'],$_POST['invoice'])));
  }

?>
