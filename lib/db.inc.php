<?php
//connect to db
/**
* @return PDO
*/

function ierg4210_DB() {

    $db = new PDO('sqlite:/var/www/cart.db');
    $db -> query("PRAGMA foreign_keys = ON;");
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

return $db;
}


?>