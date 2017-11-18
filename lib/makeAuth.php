

<?php

session_start();
function coonectDB() {

    $db = new PDO('sqlite:/var/www/cart.db');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $db;
}

function auth_process() {
	  if(!empty($_SESSION['t4210'])){
        $db = coonectDB();
        $q = $db->prepare('SELECT * FROM users WHERE email = ?');
        $q -> execute(array($_SESSION['t4210']['em']));
        if($r = $q -> fetch()){
            if($r['flag'] == 0){
               return false;
            }
            $realk=hash_hmac('sha1', $_SESSION['t4210']['exp'].$r['password'], $r['salt']);
            if($realk == $_SESSION['t4210']['k']){
                return $_SESSION['t4210']['em'];
            }
        }
			  return false;
		}
		if(!empty($_COOKIE['t4210'])){
			  //stripslashes() Returns a string with backslashes stripped off.
				// (\' becomes ' and so on.)
				if($t = json_decode(stripslashes($_COOKIE['t4210']),true)) {
						if(time() > $t['exp']){
							  return false;
						}
						$db = coonectDB();
						$q = $db->prepare('SELECT * FROM users WHERE email = ?');
						$q -> execute(array($t['em']));
						if($r = $q -> fetch()){

                if($r['flag'] == 0){
                  return false;
                }
							  $realk=hash_hmac('sha1', $t['exp'].$r['password'], $r['salt']);
							  if($realk == $t['k']){
								    $_SESSION['t4210'] = $t;
								    return $t['em'];
							  }
						}
				}
		}
		return false;
}

?>
