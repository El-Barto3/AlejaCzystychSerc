<?php
	session_start();
		//Udana walidacja? Załóżmy, że tak!
		$wszystko_OK=true;
		
		//Sprawdź poprawność imienia i nazwiska
		$name = $_POST['name'];
		$surname = $_POST['surname'];

		if( $name == "")
		{
			$wszystko_OK=false;
			$_SESSION['e_name'] = "Niepoprawne imię";
		}
			
		if($surname == "")
		{
			$wszystko_OK=false;
			$_SESSION['e_surname'] = "Niepoprawne nazwisko";
		}

		// Sprawdź poprawność adresu email
		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		
		if (filter_var($email, FILTER_VALIDATE_EMAIL) == false)
		{
			$wszystko_OK=false;
			$_SESSION['e_email']="Niepoprawny email, wzór: adres@mail.com";
		}
		
		//Sprawdź poprawność hasła
		$haslo1 = $_POST['password1'];
		$haslo2 = $_POST['password2'];
		
		if ((strlen($haslo1)<8) || (strlen($haslo1)>20))
		{
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if ($haslo1!=$haslo2)
		{
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Podane hasła nie są identyczne!";
		}	

		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		//Czy zaakceptowano regulamin?
		if (!isset($_POST['regulamin']))
		{
			$wszystko_OK=false;
			$_SESSION['e_regulamin']="Potwierdź akceptację regulaminu!";
		}				
		
		//ReCaptcha sprawdza poprawność
		$sekret = "6LfjU68aAAAAAH8lCLZGPf_yydddrb4MJbq-6phd";
		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if ($odpowiedz->success==false)
		{
			$wszystko_OK=false;
			$_SESSION['e_bot']="Potwierdź, że nie jesteś robotem!";
		}		
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_name'] = $name;
		$_SESSION['fr_surname'] = $surname;
		$_SESSION['fr_email'] = $email;
		if (isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;
		
		$host = "rlpmyaxbaza.mysql.db";
		$db_user = "rlpmyaxbaza";
		$db_password = "AGDg7ZQdtvCZbgMB";
		$db_name = "rlpmyaxbaza";	
			
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try 
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			$polaczenie -> query ('SET NAMES utf8');
			$polaczenie -> query ('SET CHARACTER_SET utf8_unicode_ci');
			if ($polaczenie->connect_errno!=0) 
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//Czy email już istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE email='$email'");
				
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					$wszystko_OK=false;
					$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
				}		

				
				if ($wszystko_OK==true)
				{
					//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
					if ($polaczenie->query("INSERT INTO users VALUES (NULL, '$name', '$surname', '$email', '$haslo_hash', CURRENT_TIMESTAMP, NULL, NULL)"))
					{
						$_SESSION['udanarejestracja']=true;
						unset($_SESSION['submit_button_register']);
						//header('Location: ?a=news');
						
					}
					else
					{
						throw new Exception($polaczenie->error);
					}
					
				}
				
				$polaczenie->close();
			}
			
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
			if(strpos($e, "mysqli") !== false) echo '<br />Informacja developerska: Błąd połączenia.';
			else echo '<br />Informacja developerska: '.$e;
		}
		
?> 