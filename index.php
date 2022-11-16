<?php
	session_start();

	include ('config.php');

	$facebook_output = '';

	$facebook_helper = $facebook->getRedirectLoginHelper();

	if (isset($_GET['code']))
	{
		if (isset($_SESSION['access_token']))
		{
			$access_token = $_SESSION['access_token'];
		}
		else
		{
			$access_token = $facebook_helper->getAccessToken();

			$_SESSION['access_token'] = $access_token;

			$facebook->setDefaultAccessToken($_SESSION['access_token']);
		}

		$_SESSION['user_id'] = '';
		$_SESSION['user_first_name'] = '';
		$_SESSION['user_last_name'] = '';
		$_SESSION['user_email_address'] = '';
		$_SESSION['user_image'] = '';

		$graph_response = $facebook->get("/me?fields=first_name,last_name,email", $access_token);
		
		print_r($facebook_user_info);
		
		$facebook_user_info = $graph_response->getGraphUser();
		$dataOK = true;
		if (!empty($facebook_user_info['id'])) 
			$_SESSION['user_image'] = 'http://graph.facebook.com/' . $facebook_user_info['id'] . '/picture';
		else 
			$dataOK = false;

		if (!empty($facebook_user_info['first_name'])) 
			$_SESSION['user_first_name'] = $facebook_user_info['first_name'];
		else 
			$dataOK = false;

		if (!empty($facebook_user_info['last_name'])) 
			$_SESSION['user_last_name'] = $facebook_user_info['last_name'];
		else 
			$dataOK = false;	

		if (!empty($facebook_user_info['email'])) 
			$_SESSION['user_email_address'] = $facebook_user_info['email'];
		else 
			$dataOK = false;
		
		if (!empty($facebook_user_info['email'])) {
			
			require "database.php";
			
			$query = $db->prepare("SELECT * FROM users WHERE email = :email");
			$query->bindParam(":email", $_SESSION['user_email_address']);
			$query->execute();
			$accountExist = $query->fetch();
			
			if ($query->rowCount() > 0)
			{
				if ($accountExist['password'] == 'facebook')
				{
					$_SESSION['logged'] = true;
					$_SESSION['loggedFB'] = true;
					$_SESSION['userID'] = $accountExist['ID'];
				}
				else
				{
					$_SESSION['e_login'] = "Istnieje już konto z podanym emailem!";
				}
			}
			else
			{
				if ($dataOK)
				{
					$query = $db->prepare("INSERT INTO users (id, name, surname, email, password, time) VALUES (NULL, :firstname, :lastname, :email, 'facebook', CURRENT_TIMESTAMP)");
					$query->bindParam(":firstname", $_SESSION['user_first_name']);
					$query->bindParam(":lastname", $_SESSION['user_last_name']);
					$query->bindParam(":email", $_SESSION['user_email_address']);
					$query->execute();
				}
				else
				{
					$_SESSION['e_login'] = "Nie udało sie utworzyć nowego konta!";
				}

			}
			$query = $db->prepare("SELECT * FROM users WHERE email = :email");
			$query->bindParam(":email", $_SESSION['user_email_address']);
			$query->execute();
			$accountExist = $query->fetch();
			
			if ($query->rowCount() > 0)
			{
				if ($accountExist['password'] == 'facebook')
				{
					$_SESSION['logged'] = true;
					$_SESSION['loggedFB'] = true;
					$_SESSION['userID'] = $accountExist['id'];
					echo "<script type='text/javascript'>window.location.href = '?a=news';</script>";
				}
				else
				{
					$_SESSION['e_login'] = "Istnieje już konto z podanym emailem!";
				}
			}
		}
		else
			$_SESSION['e_login'] = "Nie udało się pobrać danych z facebooka!";
		

	}
	else
	{
		// Get login url
		$facebook_permissions = ['email']; // Optional permissions
		$facebook_login_url = $facebook_helper->getLoginUrl('https://alejaserc.pl/', $facebook_permissions);
	}

	//normal logging registration
	if (isset($_POST['password1']))
	{
		include 'registration.php';
	}
	else if (isset($_POST['login']))
	{
		include 'login.php';
	}
	//session for logged user
	if ($_SESSION['logged'] == true)
	{
		include 'database.php';
		$query = $db->prepare("SELECT name, surname FROM users WHERE ID = :ID");
		$query->bindParam(":ID", $_SESSION['userID']);
		$query->execute();

		$user = $query->fetch();

	}
	$query = null;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-7NMSJPSQ2C"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-7NMSJPSQ2C');
</script>
	
	<script type="text/javascript">
    var xPos, yPos;
    var prm = Sys.WebForms.PageRequestManager.getInstance();
    prm.add_beginRequest(BeginRequestHandler);
    prm.add_endRequest(EndRequestHandler);
    function BeginRequestHandler(sender, args) {
        xPos = $get('scrollDiv').scrollLeft;
        yPos = $get('scrollDiv').scrollTop;
    }
    function EndRequestHandler(sender, args) {
        $get('scrollDiv').scrollLeft = xPos;
        $get('scrollDiv').scrollTop = yPos;
    }
</script>
	
	
<meta name="description" content="Dołącz do nas, zaznaj niesamowitej lekcji historii i odwiedź zabytkowy obiekt w 360 stopniach! Aleja Czystych Serc działa w ramach olimpiady Zwolnieni z Teorii.">
<meta name="keywords" content="aleja serc, aleja czystych serc, cmentarz, zwzt, zwolnieni z teorii, jaroslaw">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style.css" /><br>
<link rel="stylesheet" href="css/entry.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" />
<script src="https://code.jquery.com/jquery-3.5.1.js"></script> 
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script> 
<script src="https://www.google.com/recaptcha/api.js" async defer></script> 
<script src="scripts/wpis.js"></script> 
<script type="text/javascript">

			<?php if(isset($_POST['submit_button_register']) && $_SESSION['udanarejestracja']==false) { ?> 

						$(function() {                     
							$('#registerModal').modal('show');     
						});

			<?php } ?>       


			<?php if((isset($_SESSION['e_login']) || isset($_POST['submit_button_login'])) &&($_SESSION['logged'] == false)) { ?> 

						$(function() {                     
							$('#loginModal').modal('show');     
						});

			<?php } ?>

			<?php if($_SESSION['udanarejestracja'] == true) { ?> 

						$(function() {                     
							$('#successModal').modal('show');     
						});
			
			<?php 	
				unset( $_SESSION[ 'udanarejestracja' ] );
			}
			?>  			

        </script>
	
</head>
<body class="" background="images/background.jpg">
<div id="carouselExampleIndicators" class="carousel slide border-bottom border-warning"  data-ride="carousel"> 
  <!-- To jest baner  -->
  <div class="carousel-inner">
    <div class="carousel-item active"> <img class="d-block w-100 obrazek-banner" src="images/chryzentemy1.jpg" alt="Pierwszy slajd" />
      <div class="carousel-caption d-none d-md-block cytaty">
        <h5>„Śmierć nie istnieje, dopóki nie nadejdzie”.</h5>
        <p>– Stefan Kisielewski</p>
      </div>
    </div>
    <div class="carousel-item"> <img class="d-block w-100 obrazek-banner" src="images/chryzentemy2.jpg" alt="Drugi slajd" />
      <div class="carousel-caption d-none d-md-block cytaty">
        <h5>„Śmierć nadaje sens życiu, dlatego trzeba mieć ją zawsze przy sobie – w myśli”.</h5>
        <p>– Stefan Kisielewski</p>
      </div>
    </div>
    <div class="carousel-item"> <img class="d-block w-100 obrazek-banner" src="images/chryzentemy3.jpg" alt="Trzeci slajd" />
      <div class="carousel-caption d-none d-md-block cytaty">
        <h5>„Gdyby śmierci nie było, nikt z nas by już nie żył. Przemijamy jak wszystko, by w ten sposób przetrwać”.</h5>
        <p>– Jan Twardowski</p>
      </div>
    </div>
    <div class="carousel-item"> <img class="d-block w-100 obrazek-banner" src="images/chryzentemy4.jpg" alt="Czwarty slajd" />
      <div class="carousel-caption d-none d-md-block cytaty">
        <h5>„Bo żyć możesz tylko tym, za co zgodzisz się umrzeć”.</h5>
        <p>- Antoine de Saint-Exupéry</p>
      </div>
    </div>
    <div class="carousel-item"> <img class="d-block w-100 obrazek-banner" src="images/chryzentemy5.jpg" alt="Piąty slajd" />
      <div class="carousel-caption d-none d-md-block cytaty">
        <h5>„Śmierć nie jest przeciwieństwem życia, a jego częścią”.</h5>
        <p>- Haruki Murakami</p>
      </div>
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
  <ol class="carousel-indicators indykatoryBanera">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
  </ol>
</div>
<div class="container-fluid">
  <div class="row no-gutter sticky-top">
    <div class="col-sm-4"></div>
    <div class="logodiv col-md-1 col-sm-2"><img src="images/logo.png" style="width: 100%; height: auto; transform: rotate(180deg);" class="" /></div>
    <div class="col-sm-4"></div>
  </div>
</div>
<div class="row w-100 text-center mx-auto my-3">
	<div class="container-fluid">
<?php
    
if ( $_SESSION['logged'] == true ) {
	if($_SESSION['loggedFB'])
		echo '<img src="'.$_SESSION["user_image"].'" class="rounded-circle" />';
	
	echo "<div class='text-light'>Zalogowano jako ".$user['name']." ".$user['surname']."</div>";
}
	

?>
	</div>
	</div>
<div class="container-fluid"> 
  <!-- Tutaj jest faktyczna treść strony -->
  <div class="row no-gutters">
    <div class="col-sm-2 d-none d-md-block"></div>
    <!-- To jest cała lewa przestrzeń -->
    <div class="col-sm-8 offset-sm-2 offset-md-0">
      <header class="menuGlowne"> 
        <!-- To jest menu główne -->
        <nav class="navbar navbar-expand-lg navbar-dark"> <a class="navbar-brand menuOpcja" href="?a=news">Strona główna</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=contact">O nas</a> </li>
	          <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=rules">Regulamin</a> </li>
              <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=entry_person">Dodaj wpis</a> </li>
              <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=book">Księga dusz</a> </li>
              <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=walk">Spacer wirtualny</a> </li>
            </ul>
            <?php
            if ( $_SESSION['logged'] == false ) {
              ?>
            <ul class="navbar-nav">
              <li class="nav-item active"> <a class="nav-link menuOpcja" style="cursor: pointer" data-toggle="modal" data-target="#loginModal" data-backdrop="static">Zaloguj się</a> </li>
              <li class="nav-item active"> <a class="nav-link menuOpcja" style="cursor: pointer" data-toggle="modal" data-target="#registerModal" data-backdrop="static">Zarejestruj się</a> </li>
            </ul>
            <?php } else{?>
            <ul class="navbar-nav">
              <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=logout">Wyloguj się</a> </li>
            </ul>
            <?php
            }
            ?>
          </div>
        </nav>
      </header>
      <div class="text-light bg-dark border-warning rounded border-left border-right srodek"> 
        <!-- To jest środkowa część strony -->
        <div class="container-fluid">
          <?php
          include 'content.php';
          ?>
        </div>
      </div>
    </div>
    <div class="col-sm-2 d-none d-md-block"></div>
    <!-- To jest prawa część strony --> 
  </div>
</div>
<footer class="container stopka border-top border-warning">
  <div class="row">
    <div class="col-sm-12 text-light"> All rights reserved. &copy; 2021 </div>
  </div>
</footer>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-dark">
      <div class="modal-header border-bottom-0 text-light">
        <button type="button" class="close text-warning" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body">
        <div class="form-title text-center text-light">
          <h4>Zaloguj się</h4>
        </div>
        <div class="d-flex flex-column text-center">
          <form method="post">
            <?php
            if ( isset( $_SESSION[ 'e_login' ] ) ) {
              echo '<div class="error form-group col" style="color: red;">' . $_SESSION[ 'e_login' ] . '</div>';
              unset( $_SESSION[ 'e_login' ] );
            }
            ?>
            <div class="form-group">
              <input type="email" class="form-control" placeholder="Twój e-mail..." name="login" />
            </div>
            <div class="form-group">
              <input type="password" class="form-control" placeholder="Twoje hasło...." name="password" />
            </div>
            <button type="submit" name="submit_button_login" class="btn btn-warning btn-block btn-round">Zaloguj się</button>
          </form>
			<div class="d-flex justify-content-center social-buttons mb-3">
					<?php
						echo '<a href="'.$facebook_login_url.'"><button type="button" class="btn btn-primary btn-round mt-3" data-toggle="tooltip" data-placement="top" title="Facebook">Albo użyj facebooka <i class="bi bi-facebook"></i></button></a>';					
					?>				
			</div>
          
        </div>
      </div>
      <div class="signup-section text-center text-light pb-3">Nie masz konta? <a href="#a" class="text-warning" data-dismiss="modal" data-toggle="modal" data-target="#registerModal"> Zarejestruj się!</a></div>
    </div>
  </div>
</div>
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-dark">
      <div class="modal-header border-bottom-0 text-light">
        <button type="button" class="close text-warning" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body">
        <div class="form-title text-center text-light">
          <h4>Zarejestruj się</h4>
          <h5>Bo razem tworzymy jedność!</h5>
        </div>
        <div class="d-flex flex-column text-center">
          <form method="post">

            <div class="row"> </div>
            <div class="row">
              <div class="form-group col">
                <?php
                if ( isset( $_SESSION[ 'e_name' ] ) ) {
                  echo '<div class="error text-danger">' . $_SESSION[ 'e_name' ] . '</div>';
                  unset( $_SESSION[ 'e_name' ] );
                }
                ?>
                <input autocomplete="given-name" type="#" class="form-control" id="log" placeholder="Twoje imię..." name="name" value="<?php
												if (isset($_SESSION['fr_name']))
												{
													echo $_SESSION['fr_name'];
													unset($_SESSION['fr_name']);
												}
											?>"/>
              </div>
              <div class="form-group col">
                <?php
                if ( isset( $_SESSION[ 'e_surname' ] ) ) {
                  echo '<div class="error  text-danger">' . $_SESSION[ 'e_surname' ] . '</div>';
                  unset( $_SESSION[ 'e_surname' ] );
                }
                ?>
                <input autocomplete="family-name" type="#" class="form-control" id="naz" placeholder="Twoje nazwisko..." name="surname" value="<?php
												if (isset($_SESSION['fr_surname']))
												{
													echo $_SESSION['fr_surname'];
													unset($_SESSION['fr_surname']);
												}
											?>"/>
              </div>
            </div>
            <?php
            if ( isset( $_SESSION[ 'e_email' ] ) ) {
              echo '<div class="error text-danger">' . $_SESSION[ 'e_email' ] . '</div>';
              unset( $_SESSION[ 'e_email' ] );
            }
            ?>
            <div class="form-group">
              <input autocomplete="new-email" type="email" class="form-control" placeholder="Twój e-mail..." name="email" value="<?php
											if (isset($_SESSION['fr_email']))
											{
												echo $_SESSION['fr_email'];
												unset($_SESSION['fr_email']);
											}
										?>"/>
            </div>
            <?php
            if ( isset( $_SESSION[ 'e_haslo' ] ) ) {
              echo '<div class="error text-danger">' . $_SESSION[ 'e_haslo' ] . '</div>';
              unset( $_SESSION[ 'e_haslo' ] );
            }
            ?>
            <div class="form-group">
              <input type="password" class="form-control" placeholder="Twoje hasło..." name="password1"/>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" placeholder="Potwierdź hasło..." name="password2" />
            </div>
            <?php
            if ( isset( $_SESSION[ 'e_bot' ] ) ) {
              echo '<div class="error" style="color: red;">' . $_SESSION[ 'e_bot' ] . '</div>';
              unset( $_SESSION[ 'e_bot' ] );
            }
            ?>
            <div class="form-group text-xs-center">
              <div class="g-recaptcha" data-sitekey="6LfjU68aAAAAANvY8AttqWZjO2jC6bwk30EL-Dby"></div>
            </div>
            <?php
            if ( isset( $_SESSION[ 'e_regulamin' ] ) ) {
              echo '<div class="error text-danger">' . $_SESSION[ 'e_regulamin' ] . '</div>';
              unset( $_SESSION[ 'e_regulamin' ] );
            }
            ?>
            <div class="form-check text-light">
              <label>
                <input type="checkbox" name="regulamin" <?php
									if (isset($_SESSION['fr_regulamin']))
									{
										echo "checked";
										unset($_SESSION['fr_regulamin']);
									}
										?>/>
                Zapoznałem się z <a href="?a=rules" class="text-warning">regulaminem korzystania z serwisu. </a> </label>
            </div>
            <button type="submit"  name="submit_button_register" class="btn btn-warning btn-block btn-round">Zarejestruj się</button>
          </form>
          <div class="d-flex justify-content-center social-buttons mb-3"></div>
        </div>
      </div>
      <div class="signup-section text-center text-light pb-3"> Masz już konto? <a href="#a" class="text-warning" data-dismiss="modal" data-toggle="modal" data-target="#loginModal"> Zaloguj się!</a></div>
    </div>
  </div>
</div>
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-dark">
      <div class="modal-header border-bottom-0 text-light">
        <button type="button" class="close text-warning" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body">
        <div class="form-title text-center text-success">
          <h4>Rejestracja udana</h4>
        </div>
      </div>
      <div class="signup-section text-center text-light pb-3"><a href="#a" class="text-warning" data-dismiss="modal" data-toggle="modal" data-target="#loginModal"> Zaloguj się!</a></div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
  $('#log').change(function() {
    var container1 = '',
      container2 = '',
      container3 = '',
      login = $('#log').val().toLowerCase().split(' ');
    var iterator = login.values();
    for (var i = 0; i < login.length; i++) {
      container1 = iterator.next().value;
      container2 = container1.charAt(0).toUpperCase() + container1.slice(1);
      container3 = container3 + container2 + " ";
    }
    $('#log').val(container3);
  });
  $('#naz').change(function() {
    var container1 = '',
      container2 = '',
      container3 = '',
      login = $('#naz').val().toLowerCase().split(' ');
    var iterator = login.values();
    for (var i = 0; i < login.length; i++) {
      container1 = iterator.next().value;
      container2 = container1.charAt(0).toUpperCase() + container1.slice(1);
      container3 = container3 + container2 + " ";
    }
    $('#naz').val(container3);
  });
});
</script>
</body>
</html>
