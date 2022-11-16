<?php

	session_start();
	
	if ((!isset($_POST['login'])) || (!isset($_POST['password'])))
	{
		header("Location: index.php");
	}
		
	include 'databaselogin.php';

	$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
	if ($polaczenie->connect_errno!=0)
	{
		throw new Exception(mysqli_connect_errno());
	}
	else
	{
		$login = $_POST['login'];
		$password = $_POST['password'];
		
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
	
		if ($rezultat = @$polaczenie->query(
		sprintf("SELECT * FROM users WHERE email='%s'",
		mysqli_real_escape_string($polaczenie,$login))))
		{
			$ilu_userow = $rezultat->num_rows;
			if($ilu_userow>0)
			{
				$wiersz = $rezultat->fetch_assoc();
				if(password_verify($password, $wiersz['password']))
				{
					if($wiersz['banned'] == false)
					{
						$date_now = date("Y-m-d H:i:s");
						$suspended = $wiersz['suspended'];
						echo $date_now." > ".$suspended;
						if($suspended < $date_now)
						{
							//tu sie powinno stać coś jak zadziała logowanie
							$_SESSION['logged'] = true;
							$_SESSION['userID'] = $wiersz['id'];
							//unset($_SESSION['e_login']);
							$rezultat->free_result();
						}
						else {
							$_SESSION['e_login'] = "Twoje konto zostało zawieszone do ".$wiersz['suspended']."!";
						}	
					}	
					else {
						$_SESSION['e_login'] = "Twoje konto zostało zablokowane!";
					}
				}
				else 
				{
					$_SESSION['e_login'] = "Niepoprawny login lub hasło!";
				}
				
			} else {
				
				$_SESSION['e_login'] = "Niepoprawny login lub hasło!";
			}
			
		}
		else
			echo '<script>alert("Error")</script>'; 
		
		$polaczenie->close();
	}


?>