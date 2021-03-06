<?php

session_start();
include_once ('lib/csrf.php');
include_once ('lib/db.inc.php');
include_once ('lib/makeAuth.php');


if(!auth_process()){
	header('Location: login.php');
	exit();
}


function ierg4210_cat_fetchall() {
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM categories LIMIT 100;");
	if ($q->execute())
		return $q->fetchAll();
}

function ierg4210_cat_insert() {

	//TODO: input validation or sanitization using Regex
	if(!preg_match('/^[\w\-, ]+$/', $_POST['name']))
		throw new Exception("invalid-name");

	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("INSERT INTO categories (name) VALUES (?)");
	return $q->execute(array($_POST['name']));
}

function ierg4210_cat_edit() {

	// TODO: complete the rest of this function; it's now always says "successful" without doing anything
    if(!preg_match('/^[\w\-, ]+$/', $_POST['name']))
        throw new Exception("invalid-name");

		if (!preg_match('/^\d*$/', $_POST['catid']))
				throw new Exception("invalid-catid");

    global $db;
    $db = ierg4210_DB();
    $_POST['catid'] = (int)$_POST['catid'];
    $q = $db->prepare("UPDATE categories SET name = ? WHERE catid = ?;");

    return $q->execute(array($_POST['name'],$_POST['catid']));

}

function ierg4210_cat_delete() {

	$_POST['catid'] = (int) $_POST['catid'];

	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("DELETE FROM categories WHERE catid = ?");
	return $q->execute(array($_POST['catid']));
}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
	// input validation or sanitization
	// DB manipulation
	global $db;
	$db = ierg4210_DB();

	// TODO: complete the rest of the INSERT command
	if (!preg_match('/^\d*$/', $_POST['catid']))
		throw new Exception("invalid-catid");
	$_POST['catid'] = (int) $_POST['catid'];
	if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
		throw new Exception("invalid-name");
	if (!preg_match('/^[\d\.]+$/', $_POST['price']))
		throw new Exception("invalid-price");
	if (!preg_match('/^[\w\-\. ]+$/', $_POST['description']))
		throw new Exception("invalid-text");

	$sql="INSERT INTO products (catid, name, price, description) VALUES (?, ?, ?, ?)";
	$q = $db->prepare($sql);
	//$q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description']));

	// The lastInsertId() function returns the pid (primary key) resulted by the last INSERT command
	//$lastId = $db->lastInsertId();
	// Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg

	if ($_FILES["file"]["error"] == 0
		&& $_FILES["file"]["type"] == "image/jpeg"
		&& mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg"
		&& $_FILES["file"]["size"] < 5000000) {

		$q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description']));
		$lastId = $db->lastInsertId();


		// Note: Take care of the permission of destination folder (hints: current user is apache)
		if (move_uploaded_file($_FILES["file"]["tmp_name"], "incl/img/" . $lastId . ".jpg")) {
		// redirect back to original page; you may comment it during debug
			//echo " move success ";
			header('Location: admin.php');
			exit();
		}
	}
	// Only an invalid file will result in the execution below
	// To replace the content-type header which was json and output an error message

	header('Content-Type: text/html; charset=utf-8');
	echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';

	exit();
}





// TODO: add other functions here to make the whole application complete


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


function ierg4210_prod_edit() {
    global $db;
    $db = ierg4210_DB();

		if (!preg_match('/^\d*$/', $_POST['pid']))
				throw new Exception("invalid-pid");
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    if (!preg_match('/^[\w\-\. ]+$/', $_POST['description']))
        throw new Exception("invalid-text");

    $_POST['pid'] = $_POST['pid'];
    $q = $db->prepare("UPDATE products SET catid=?,name=?,price=?,description=? WHERE pid = ?;");

    if($_FILES["cover_image"]["size"]==0){

			$q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description'],$_POST['pid']));
			header('Location: admin.php');
			exit();

		}


		else if ($_FILES["file"]["error"] == 0
			&& $_FILES["file"]["type"] == "image/jpeg"
			&& mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg"
			&& $_FILES["file"]["size"] < 5000000) {

			$q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description'],$_POST['pid']));

			// Note: Take care of the permission of destination folder (hints: current user is apache)
			if (move_uploaded_file($_FILES["file"]["tmp_name"], "incl/img/" . $_POST['pid']. ".jpg")) {
			// redirect back to original page; you may comment it during debug
				//echo " move success ";
				header('Location: admin.php');
				exit();
			}
		}
		// Only an invalid file will result in the execution below
		// To replace the content-type header which was json and output an error message

		header('Content-Type: text/html; charset=utf-8');
		echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';

		exit();


}

function ierg4210_prod_delete() {

	$_POST['pid'] = (int) $_POST['pid'];

    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("DELETE FROM products WHERE pid = ?");
    return $q->execute(array($_POST['pid']));

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

	//input validation
	if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
			throw new Exception('Undefined Action');
	}

	//check if the form request can present a valid nonce
	if($_REQUEST['action'] == 'cat_insert' || $_REQUEST['action'] == 'cat_edit' || $_REQUEST['action'] == 'prod_insert' || $_REQUEST['action'] == 'prod_edit') {
			csrf_verifyNonce($_REQUEST['action'],$_POST['nonce']);
	}


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
