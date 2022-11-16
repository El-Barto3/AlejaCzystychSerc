<?php

	session_start();

	$createEntryFuneral = getVariable( "createEntryFuneral" );

	if ( $createEntryFuneral == 1 ) {

		include 'database.php';

		$funeralName = replaceBlank( sanitizeText( $_POST[ "name" ] ) );
		$funeralSurname = replaceBlank( sanitizeText( $_POST[ "surname" ] ) );
		$funeralDate = replaceBlank( sanitizeText( $_POST[ "funeral_date" ] ) );
		$funeralPlace = replaceBlank( sanitizeText( $_POST[ "funeral_place" ] ) );
		$funeralAge = replaceBlank( sanitizeText( $_POST[ "age" ] ) );
		$funeralRosary = replaceBlank( sanitizeText( $_POST[ "rosary" ] ) );

		$ip = isset( $_SERVER[ "HTTP_CF_CONNECTING_IP" ] ) ? $_SERVER[ "HTTP_CF_CONNECTING_IP" ] : $_SERVER[ "REMOTE_ADDR" ];
	 
		//how many activities so far (+1)
		$activities = ($db->query('select count(*) from activity')->fetchColumn()) + 1;
		
		//inserting new activity into activity table
		insertIntoActivity($activities, 'funerals');
		
		//database creating record in funeral & activity
		if ( $_FILES[ "image" ]["name"] != "" && isset($_FILES[ "image" ]))
			$avatarUrl = uploadAvatar();
		else
			$avatarUrl = "/images/defaultPicture.png";
		
		//$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		$qry = $db->prepare( "INSERT INTO funerals (ID, entry_key, name, lastname, date, place, age, description, picture_url) VALUES (NULL, :entry_key, :name, :surname, :date, :place, :age, :description, :pictureURL)" );
		$qry->bindParam( ":entry_key", $activities );
		$qry->bindParam( ":name", $funeralName );
		$qry->bindParam( ":surname", $funeralSurname );
		$qry->bindParam( ":date", $funeralDate );
		$qry->bindParam( ":place", $funeralPlace );
		$qry->bindParam( ":age", $funeralAge );
		$qry->bindParam( ":description", $funeralRosary );
		$qry->bindParam( ":pictureURL", $avatarUrl );
		$qry->execute();
		

		
		$response = "<span class='text-success'>Pomyślnie przesłano wpis, po sprawdzeniu będzie on dostępny w księdze zmarłych</span>";

	}
?>
<div class="row">
  <div class="col-sm-12 aktualnosciNaglowek border-bottom border-warning">
    <h1>Dodaj wpis</h1>
  </div>
</div>
<div class="createEntry">

  <nav class="navbar navbar-expand-lg navbar-dark">
    <button class="navbar-toggler w-100" type="button" data-toggle="collapse" data-target="#navbarDrugi" aria-controls="navbarDrugi" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
    <div class="collapse navbar-collapse" id="navbarDrugi">
      <ul class="navbar-nav mx-auto mt-3 mb-3 border-bottom border-warning">
        <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=entry_person">Osoba</a> </li>
        <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=entry_event">Wydarzenie</a> </li>
        <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=entry_funeral">Pogrzeb</a> </li>
        <li class="nav-item active"> <a class="nav-link menuOpcja" href="?a=entry_info">Ogłoszenie</a> </li>
      </ul>
    </div>
  </nav>
	  <?php
  if ( $_SESSION[ 'logged' ] ) {
    ?>
	  	<div class="row text-center">
	  <h3 class="text-break mx-auto"><?=$response?></h3>
		</div>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-11 col-sm-9 col-md-7 col-lg-6 col-xl-5 text-center p-0 mt-3 mb-2 bg-dark">
        <div class="card px-3 pt-4 pb-0 mt-3 mb-3 bg-dark">
          <h2 id="heading" class="text-warning">Dodaj klepsydrę</h2>
          <p>Wypełnij wszystkie dane oznaczone *gwiazdką</p>
          <form id="msform" method='post' enctype="multipart/form-data" >
            <!-- progressbar -->
			<input type="hidden" name="createEntryFuneral" value="1" />
            <ul id="progressbar">
              <li class="active" id="account"><strong>Dane podstawowe</strong></li>
              <li id="personal"><strong>Dane opcjonalne</strong></li>
              <li id="payment"><strong>Zdjęcie</strong></li>
              <li id="confirm"><strong>Koniec</strong></li>
            </ul>
            <div class="progress">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <br>
            <!-- fieldsets -->
            <fieldset class="bg-dark" id="jeden">
              <div class="form-card bg-dark" id="dwa">
                <div class="row">
                  <div class="col-7">
                    <h2 class="fs-title text-warning">Dane podstawowe</h2>
                  </div>
                  <div class="col-5">
                    <h2 class="steps">Krok 1 - 4</h2>
                  </div>
                </div>
                <label class="fieldlabels">Imię zmarłego *</label>
                <input type="text" name="name" placeholder="Imię..." class="compInput"/>
                <label class="fieldlabels">Nazwisko zmarłego *</label>
                <input type="text" name="surname" placeholder="Nazwisko..." class="compInput"/>
                <label class="fieldlabels">Data pogrzebu *</label>
                <input type="date" name="funeral_date" placeholder="Data pogrzebu..." class="compInput"/>
                <label class="fieldlabels">Miejsce pogrzebu *</label>
                <input type="text" name="funeral_place" placeholder="Miejsce pogrzebu..." class="compInput"/>
              </div>
              <input type="button" name="next" class="next action-button" value="Dalej" id="eventButtonOne" disabled="disabled"/>
            </fieldset>
            <fieldset class="bg-dark  ">
              <div class="form-card bg-dark">
                <div class="row">
                  <div class="col-7">
                    <h2 class="fs-title text-warning">Dane dodatkowe</h2>
                  </div>
                  <div class="col-5">
                    <h2 class="steps">Krok 2 - 4</h2>
                  </div>
                </div>
                <label class="fieldlabels">Przeżył/a </label>
                <input type="text" name="age" placeholder="Wiek..." />
                <label class="fieldlabels">Informacje o różańcu </label>
                <textarea type="text" name="rosary" placeholder="Informacje dotyczące różańca..." rows="15"></textarea>
              </div>
              <input type="button" name="next" class="next action-button" value="Dalej" />
              <input type="button" name="previous" class="previous action-button-previous" value="Poprzedni" />
            </fieldset>
            <fieldset class="bg-dark  ">
              <div class="form-card bg-dark">
                <div class="row">
                  <div class="col-7">
                    <h2 class="fs-title text-warning">Dodaj zdjęcie</h2>
                  </div>
                  <div class="col-5">
                    <h2 class="steps">Krok 3 - 4</h2>
                  </div>
                </div>
                <div class="row no-gutter my-5 py-5 mx-auto">
                  <div class="file-drop-area text-center mx-auto border border-secondary rounded" id="plik-menu" > <span class="choose-file-button" style="z-index: 0">Wybierz plik</span> <span class="file-message" style="z-index: 0" id="avbat">lub przeciągnij go tutaj</span>
                    <input id="avatar" type="file" name="image" class="file-input" style="z-index: 999" accept=".jfif,.jpg,.jpeg,.png,.gif">
                  </div>
                  <div id="divImageMediaPreview"> </div>
                </div>
              </div>
              <input type="submit" name="next" class="next action-button" value="Potwierdź" />
              <input type="button" name="previous" class="previous action-button-previous" value="Poprzedni" />
            </fieldset>
            <fieldset class="bg-dark  ">
              <div class="form-card bg-dark">
                <div class="row">
                  <div class="col-7"> </div>
                  <div class="col-5">
                    <h2 class="steps">Krok 4 - 4</h2>
                  </div>
                </div>
                <br>
                <br>
                <h2 class="purple-text text-center"><strong>SUKCES !</strong></h2>
                <br>
                <div class="row justify-content-center">
                  <div class="col-3"> <img src="https://i.imgur.com/GwStPmg.png" class="fit-image"> </div>
                </div>
                <br>
                <br>
                <div class="row justify-content-center">
                  <div class="col-7 text-center">
                    <h5 class="purple-text text-center">Udało ci się dodać wpis</h5>
                  </div>
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
	 <?php } else{ ?>
	  		<div class="row text-center">
	  <h3 class="text-break mx-auto"><a class="text-decoration-none text-warning" data-toggle="modal" data-target="#loginModal" data-backdrop="static" style="cursor: pointer">Zaloguj się</a>, aby móc skorzystać z tej funkcji</h3>
		</div>
 <?php } ?>
</div>
<script type="text/javascript">
const avatar = document.getElementById('avatar');
const fileChosen = document.getElementById('file-chosen');
avatar.addEventListener('change', function(){
	fileChosen.textContent = this.files[0].name
})
</script>