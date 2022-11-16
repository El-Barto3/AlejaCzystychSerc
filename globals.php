<?php
	session_start();

	include 'database.php';

	$pageMainURL = "https://bartoporfolio.pl/alejaserc";

	$regex = '/^[!@#$%^&*()a-z0-9ąćęłńóśżźĄĆĘŁŃÓŚŻŹ ,_+{}|:<>?\-=[\]\;.\/\']+$/mi';
	$ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

	function insertIntoActivity( $activityNumber, $tableRef) {
		
		include 'database.php';
		$ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
		
		$qry = $db->prepare( "INSERT INTO activity (ID, entry_key, table_name, ip, owner_id, time, reports, hidden) VALUES (NULL, :entry_key, :table_ref, :ip, :owner, CURRENT_TIMESTAMP, 0, 0)");
		$qry->bindParam( ":entry_key", $activityNumber );
		$qry->bindParam( ":table_ref", $tableRef );
		$qry->bindParam( ":ip", $ip );
		$qry->bindParam( ":owner", $_SESSION[ 'userID' ] );
		$qry->execute();
	}

	function replaceBlank( $var ) {
	  if ( empty( $var ) || !isset( $var ) ) {
		return NULL;
	  } else {
		return $var;
	  }
	}

	function sanitizeText( $text ) {
	  $sanitized = htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	  $sanitized = strip_tags( $sanitized );
	  return $sanitized;
	}

	function changeDateOrder($date){
		$original_date = $date;
		$timestamp = strtotime($original_date);
		$new_date = date("d.m.Y", $timestamp);
		
		return $new_date;
	}

	function changeTimeOrder($date){
		$original_date = $date;
		$timestamp = strtotime($original_date);
		$new_date = date("d.m.Y H:i", $timestamp);
		
		return $new_date;
	}

	function getVariable($name, $error = "") {
		global $regex;
		
		$variable = array_key_exists($name, $_REQUEST) ? $_REQUEST[$name] : $error;
		$variable = str_replace(["\r\n", "\n"], "", $variable);
		
		if(preg_match($regex, $variable) || $error == $variable) {
			$variable = strip_tags($variable);
			return $variable;
		}
		else {
			return "";
		}
	}

	function uploadAvatar() {
	  $tmp = explode( '.', $_FILES[ 'image' ][ 'name' ] );
	  $_FILES[ 'image' ][ 'extension' ] = strtolower( end( $tmp ) );
	  $_FILES[ 'image' ][ 'name' ] = "avatar_" . md5( microtime( true ) . " - " . mt_rand( 0, 99999999 ) ) . ".png";

	  if (
		( $_FILES[ 'image' ][ 'type' ] == "image/gif" ) &&
		( $_FILES[ 'image' ][ 'type' ] == "image/jpeg" ) &&
		( $_FILES[ 'image' ][ 'type' ] == "image/jpg" ) &&
		( $_FILES[ 'image' ][ 'type' ] == "image/png" )
	  ) {
		$response = "Zły typ przesyłanego pliku";
		return $response;
	  }

	  if ( in_array( $_FILES[ 'image' ][ 'extension' ], [ "jpeg", "jpg", "png", "gif", "" ] ) === false ) {
		$response = "Złe rozszerzenie przesyłanego pliku";
		return $response;
	  }

	  if ( $_FILES[ 'image' ][ 'size' ] > 4194304 ) {
		$response = "Przesyłany plik jest za duży (> 4MB)";
		return $response;
	  }

	  /*$data = getimagesize($_FILES['image']['tmp_name']);
		
	  if($data[0] / $data[1] != 1) {
		$response = "Złe wymiary";
	  }*/

	  move_uploaded_file( $_FILES[ 'image' ][ 'tmp_name' ], "uploads/" . $_FILES[ 'image' ][ 'name' ] );
	  return "/uploads/" . $_FILES[ 'image' ][ 'name' ];
	}

	function getSessionVariable($name, $error = "") {
		global $regex;
		
		$variable = array_key_exists($name, $_SESSION) ? $_SESSION[$name] : $error;
		
		if(preg_match($regex, $variable) || $error == $variable) {
			$variable = strip_tags($variable);
			return $variable;
		}
		else {
			return "";
		}
	}


?>
<title>Aleja Czystych Serc</title>