<link rel="stylesheet" href="css/person.css" />
<link rel="stylesheet" href="css/comment.css" />
<?php

	session_start();

	include 'database.php';

	$ID = getVariable( "id" );
	$referer = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : "";
	$ip = isset( $_SERVER[ "HTTP_CF_CONNECTING_IP" ] ) ? $_SERVER[ "HTTP_CF_CONNECTING_IP" ] : $_SERVER[ "REMOTE_ADDR" ];


	$qry = $db->prepare( "SELECT * FROM book WHERE ID = :ID" );
	$qry->bindParam( ":ID", $ID );
	$qry->execute();
	$person = $qry->fetch();
	
	$query = $db->prepare( "SELECT * FROM activity WHERE entry_key = :entry_key" );
	$query->bindParam( ":entry_key", $person['entry_key'] );
	$query->execute();
	$isHidden = $query->fetch();

	if ( $qry->rowCount() > 0 && $isHidden['hidden'] == 0) {
	  
	  $qry = $db->prepare( "SELECT * FROM users WHERE ID = :userID" );
	  $qry->bindParam( ":userID", $_SESSION['userID'] );
	  $qry->execute();
	  //echo $userQry.'\n';
	  $user = $qry->fetch();
	  
	  $birthday = changeDateOrder($person['birthday']);
	  $deathday = changeDateOrder($person['deathday']);

	  $name = $user['name']." ".$user['surname'];
	  $message = sanitizeText( $_POST['message'] );

	  if ( $message != "" ) {
		  
		//how many activities so far (+1) 
		$activities = ($db->query('select count(*) from activity')->fetchColumn()) + 1;
		
		//inserting new activity into activity table
		insertIntoActivity($activities, 'comments');
		
		$qry = $db->prepare( "INSERT INTO comments (ID, entry_key, book_id, name, text) VALUES (NULL, :entry_key, :ID, :name, :text)" );
		$qry->bindParam( ":entry_key", $activities );
		$qry->bindParam( ":ID", $ID );
		$qry->bindParam( ":name", $name );
		$qry->bindParam( ":text", $message );
		$qry->execute();
		
		

		header( "Location: " . $pageMainURL . "?a=person&id=" . $ID );
	  }
	  else
	  {
		  if(isset($_POST['message']))
			$_SESSION['e_comment'] = "Wiadomość nie może pozostać pusta!";
	  }
	  
		
		
		$query = $db->prepare("SELECT entry_key FROM activity WHERE hidden <> 0 AND table_name = 'comments'");
		$query->execute();
		$hiddenComActivities = $query->fetchAll();
		
		$qry = $db->prepare( "SELECT * FROM comments WHERE book_id = :ID ORDER BY ID DESC" );
		$qry->bindParam( ":ID", $ID );
		$qry->execute();

	  $comments = $qry->fetchAll();
	
	  $qry = null;
	  $query = null;
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> 
<script>
  AOS.init();
</script>

<div class="row">
  <div class="col-sm-12 aktualnosciNaglowek border-bottom border-warning">
    <h1>
      <?=$person["name"]?>
      <?=$person["lastname"]?>
    </h1>
  </div>
</div>
<div class="row">
	

	<div class="col-md-4 col-sm-12"></div>
	<div class="col-md-4 col-sm-12 ">
		<div class="w-100 h-100 picture">
	   <img id="profilepic" src="<?=$pageMainURL?>.<?=$person["avatar_url"]?>" class="img-fluid rounded-circle border border-warning shadow-lg p-1 mb-3 mt-3 rounded picture picture_size">
	  	<div id="resurrect-div" class="overlay ctr rounded-circle"><button type="button" id="resurrect" class="btn picture text-light w-100 h-100 ozyw_przycisk noselect"><h1>Ożyw zmarłego</h1></button></div>
		</div>
	  
	  </div>
	<div class="col-md-4 col-sm-12"></div>





</div>

			
<div class="row mt-3">
	 
		  <div class="row mx-auto my-3 w-100 border-bottom border-warning text-center text-break">
		<div class="col-md-4"><h3 style="font-family: 'Trajan'">Data urodzin: <?=empty($person['birthday']) ? " brak danych" : $birthday ?></h3></div>
		<div class="col-md-4"><h3 style="font-family: 'Trajan'">Data śmierci: <?=empty($person['deathday']) ? " brak danych" : $deathday ?></h3></div>
		<div class="col-md-4"><h3 style="font-family: 'Trajan'">Zawód: <?=empty($person['occupation']) ? " brak danych" : $person['occupation'] ?></h3></div>
		  </div>
	  <div class="row w-100">
		  <div class="mx-auto w-100 px-3 text-center text-break" style="font-family: 'Hoefler Text'; line-height: 175%; font-size: 125%">
		  
	  		<?=$person["description"]?>
		   </div>
	</div>
</div>
<?php
if ( $person[ "grave_url" ] != "" ) {
  ?>
<br />
<div class="row">
  <div class="grave_url col-sm-12 aktualnosciNaglowek border-warning">
    <form id="my_form" action="?a=walk" method="post">
      <h1><a onclick="document.getElementById('my_form').submit();" class="facebookKontakt text-light menuOpcja text-decoration-none">Link do spaceru</a></h1>

      <input type="text" name="rnd" style="display: none" value="<?=$person[grave_url]?>"></input>

    </form>
  </div>
</div>
<?php
}
?>
<div class="row">
  <div class="col-sm-12 aktualnosciNaglowek border-warning border-top mt-5">

  </div>
</div>

<section>
    <div class="container">
        <div class="row">
			
			            <div class="col-md-6 offset-md-3">
                <form method='post' id="algin-form">
                    <div class="form-group text-center">
                        <h4>Zostaw komentarz!</h4> 
						<?php if ( $_SESSION[ 'logged' ] ) {?>
						<label for="message">Wiadomość</label> <textarea name="message" id="" msg cols="30" rows="15" class="form-control"></textarea>
						            <?php
										if ( isset( $_SESSION[ 'e_comment' ] ) ) {
										  echo '<div class="error form-group col" style="color: red;">' . $_SESSION[ 'e_comment' ] . '</div>';
										  unset( $_SESSION[ 'e_comment' ] );
										}
									?>
                    </div>
           
                    <div class="form-group text-center">
                        <p class="text-secondary">Zamieszczając komentarz pamiętaj o zasadach <a href="?a=rules" target="_blank" class="alert-link">netykiety</a>.</p>
                    </div>
                    <!--<div class="form-inline"> <input type="checkbox" name="check" id="checkbx" class="mr-1"> <label for="subscribe">Zapoznałem się z regulaminem, yoł</label> </div>-->
                    	
						<div class="form-group text-center"> <button type="submit" id="post" class="btn">Wstaw komentarz</button> </div>
						<?php } else{ ?>
							<div class="row text-center">
								<h3 class="text-break mx-auto"><a class="text-decoration-none text-warning" data-toggle="modal" data-target="#loginModal" data-backdrop="static" style="cursor: pointer">Zaloguj się</a>, aby móc skorzystać z tej funkcji</h3>
							</div>
						<?php } ?>
							
                </form>
            </div>
			   </div>
                <?php
				$colorChange = 0; 
				foreach ( $comments as $comment ) {
					
					$query = $db->prepare("SELECT * FROM activity WHERE hidden = 0 AND entry_key = ".$comment['entry_key']);
					$query->execute();
					$comActivities = $query->fetch();
					
					$hidden = false;
					foreach($hiddenComActivities as $hiddenAct)
					{
						if($comment['entry_key'] == $hiddenAct['entry_key'])
							$hidden = true;
					}
					if($query->rowCount() > 0)
					{
						$colorChange += 1; 
						if($colorChange % 2 == 0){
							echo '
									<div class="row w-100 py-3 " data-aos="zoom-out-up" data-aos-duration="750" data-aos-easing="linear">
								<div class="comment text-center w-100 offset-md-3 col-md-6 text-break"> <img src="images/defaultAvatar.png" alt="" class="rounded-circle" width="40" height="40">
									<h4 class="text-break">'.$comment[ "name" ].'</h4> <span>- '.date( "d.m.Y H:i", strtotime($comActivities[ "time" ]) ).'</span> <br>
									<p class="text-break border-top">' . $comment[ "text" ] . '</p>
								</div>
									</div>
							';
						}
						else {
							echo '
								<div class="row w-100 py-3" data-aos="zoom-out-up" data-aos-duration="750" data-aos-easing="linear">
							<div class="darker text-center w-100 offset-md-3 col-md-6 text-break"> <img src="images/defaultAvatar.png" alt="" class="rounded-circle" width="40" height="40">
								<h4 class="text-break">'.$comment[ "name" ].'</h4> <span>- '.date( "d.m.Y H:i", strtotime($comActivities[ "time" ]) ).'</span> <br>
								<p class="text-break border-top">' . $comment[ "text" ] . '</p>
							</div>
								</div>
							';
						}
					}
				}
				?>
     
	</div>
</section>
<?php
} else {
  echo "<h3>Wpis o podanym ID nie istnieje</h3>";
}
?>
<script src="scripts/overrideURLdeepfake.js"></script> 