<?php
require_once '../../../classes/DBHandler.php'; 
require_once '../../../config.php'; 
require_once '../../../sql.php'; 
$dbHandler = new DBHandler($dbh);
$data = $_POST;
$table = "user";
$primaryKey = "id"; // The primary key field of your table
$exclude = ['submitButton']; // Add the keys you want to exclude

// For insert
$dbHandler->insert($table, $data, $exclude);

// For update
//$id = 4; // The id of the record you want to update
//$dbHandler->update($table, $primaryKey, $id, $data, $exclude);

// For delete
//$id = 3; // Replace with the id of the record you want to delete
//$dbHandler->delete($table, $primaryKey, $id);
?>