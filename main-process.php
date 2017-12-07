<?php
session_start();
include_once('lib/db.inc.php');


function ierg4210_cat_fetchall() {
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM categories LIMIT 100;");
	if ($q->execute())
		return $q->fetchAll();
}


//fetch all products under one category by catid
function ierg4210_prod_fetch() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $catid = (int)$_REQUEST['catid'];
    $q = $db->prepare("SELECT * FROM products WHERE catid = $catid;");
    if ($q->execute())
        return $q->fetchAll();
}


//fetch one specific product by pid
function ierg4210_prod_fetchOne() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $pid = (int)$_REQUEST['pid'];
    $q = $db->prepare("SELECT * FROM products WHERE pid = $pid;");
    if ($q->execute())
        return $q->fetchAll();
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

// The following calls the appropriate function based to the request parameter $_REQUEST['action'],
//   (e.g. When $_REQUEST['action'] is 'cat_insert', the function ierg4210_cat_insert() is called)
// the return values of the functions are then encoded in JSON format and used as output
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
