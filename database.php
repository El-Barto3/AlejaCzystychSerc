<?php
include 'databaselogin.php';
try {
	$db = new PDO("mysql:host=$host;dbname=$db_name", "$db_user", "$db_password");;
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$db->query("SET NAMES utf8");
} catch (PDOException $e) {
	//die("Error!");
	die("Error!: " . $e->getMessage());
}

?>
