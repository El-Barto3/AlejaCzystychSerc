<?php

	session_start();

	include 'database.php';

	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
	
	$qry = $db->prepare("SELECT * FROM activity WHERE hidden<>1 ORDER BY ID DESC");
	$qry->execute();
	
	$activities = $qry->fetchAll();

?>
<div class="row">
	<div class="col-sm-12 aktualnosciNaglowek border-bottom border-warning"><h1 class="h1">Aktualności</h1></div>
</div>
<link rel="stylesheet" href="css/news.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" >
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> 
<script>
  AOS.init();
$(document).ready(function() {
  for (var i = 0; i < $('.userMess').length; i++) {
    let val = $('.userMess').eq(i).html();
    if (val.includes("https://") || val.includes("http://")) {
      let charLoc = $('.userMess').eq(i).html().lastIndexOf("http");
      let textMaker = "";
      do {
        textMaker += $('.userMess').eq(i).html().charAt(charLoc);
        charLoc += 1;
      } while ($('.userMess').eq(i).html().charAt(charLoc) != ' ' && $('.userMess').eq(i).html().charAt(charLoc) != '' && charLoc < 1000);
      var link = '<a href=' + '\"' + textMaker + '\"' + "class='text-warning'>" + textMaker + '</a>';
      var htmlVal = $('.userMess').eq(i).html().slice(0, $('.userMess').eq(i).html().lastIndexOf("http")) + link + $('.userMess').eq(i).html().slice(charLoc);
      $('.userMess').eq(i).html(htmlVal);
    }
  }
});
</script>
			<div class="container text-break">
				
	<?php		
	foreach($activities as $activity) {
		
		$qryText = "SELECT * FROM ".$activity['table_name']." WHERE entry_key = ".$activity['entry_key'];
		$query = $db->prepare($qryText);						
		$query->execute();

		$entry = $query->fetch();
		
		$qryText = "SELECT * FROM users WHERE id = ".$activity['owner_id'];
		$query = $db->prepare($qryText);						
		$query->execute();

		$owner = $query->fetch();
		
		$tableName = $activity['table_name'];
		if($tableName == 'book') {
			$link = $pageMainURL."?a=person&id=".$entry['ID'];
			echo 
				'
					<div class="row my-5" data-aos="zoom-out-up" data-aos-duration="1000" data-aos-easing="linear">
						<div style="cursor: pointer;" onclick="window.location=\''.$link.'\'" class="border-bottom rounded border-warning person p-lg-3 p-1 col-md-6 offset-md-3">
							<div class="d-none d-md-flex p-0 m-0 row w-100">
								<div class="text-left w-75 ">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>
								<div class="date text-right w-25">'.changeTimeOrder($activity["time"]).'</div>
							</div>

							<div class="d-block d-md-none p-0 m-0 row w-100">
								<div class="text-left w-100 ">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>
								<div class="date text-right w-100">'.changeTimeOrder($activity["time"]).'</div>
							</div>
							<div class="mb-5 mt-1">
									<div class="text-center"><h5>Kategoria: Zmarły</h5></div>
							</div>	
							<div class="col-lg-6 float-left">										
								<div class="">
									<div class="text-center">
										<h3><a class="text-decoration-none text-warning" href="'.$link.'">'.$entry["name"]." ".$entry['lastname'].'</a></h3>
									</div>
								</div>
													
							</div>
							<div class="col-lg-6 float-right">
								<img src="'.$pageMainURL."/alejaserc".$entry['avatar_url'].'" alt="avatar" class="rounded" style="width: 100%; height: auto; min-width: 100px">
							</div>
						</div>
					</div>
					
				';
		}
		if($tableName == 'funerals') {
			$ageText = ($entry["age"] != NULL) ? "Przeżył: ".$entry['age']." lat" : "";
			echo 
			'
			<div class="row my-5" data-aos="zoom-out-up" data-aos-duration="1000" data-aos-easing="linear">
				<div class="border-bottom rounded border-warning funerals p-lg-3 p-1 col-md-6 offset-md-3">
					<div class="d-none d-md-flex p-0 m-0 row w-100">
						<div class="text-left w-75 ">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>
												<div class="date text-right w-25">'.changeTimeOrder($activity["time"]).'</div>
					
					</div>
									<div class="d-block d-md-none p-0 m-0 row w-100">
						<div class="text-left w-100 ">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>

												<div class="date text-right w-100">'.changeTimeOrder($activity["time"]).'</div>
					
					</div>
					
					
					
					
					<div class="mb-5 mt-1">
					<div class="text-center"><h5>Kategoria: Pogrzeb</h5></div>
					</div>	
					<div class="col-lg-6 float-left">										
						<div class="">
							<div class="text-center">
								<h3><a class="text-decoration-none text-warning">'.$entry["name"]." ".$entry['lastname'].'</a></h3>
								<h5>'.$ageText.'</h5>
								
								<div class="text-center userMess">
								<p class="mb-0 pb-0 text-warning">Różaniec</p>
								
								'.$entry["description"].'
								
								</div>

							</div>
						</div>
											
					</div>
					<div class="col-lg-6 float-right">
						<img src="'.$entry['picture_url'].'" alt="avatar" class="rounded" style="width: 100%; height: auto; min-width: 100px">
					</div>
				</div>
			</div>									
			';
		}
		if($tableName == 'events') {
			$dateText = ($entry["date"] != NULL) ? "Data: ".$entry['date'] : "";
			$placeText = ($entry["place"] != NULL) ? "Miejsce: ".$entry['place'] : "";
			echo 
			'
			<div class="row my-5" data-aos="zoom-out-up" data-aos-duration="1000" data-aos-easing="linear">
				<div class="border-bottom rounded border-warning events  p-lg-3 p-1 col-md-6 offset-md-3">
					<div class="d-none d-md-flex p-0 m-0 row w-100">
						<div class="text-left w-75">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>
					<div class="date text-right w-25">'.changeTimeOrder($activity["time"]).'</div>
					</div>
					
					
					<div class="d-block d-md-none p-0 m-0 row w-100">
						<div class="text-left w-100">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>

					<div class="date text-right w-100">'.changeTimeOrder($activity["time"]).'</div>
					</div>
					
					<div class="mb-5 mt-1">
						<div class="text-center"><h5>Kategoria: Wydarzenie</h5></div>
						
					</div>	
					<div class="col-lg-6 float-left">										
						<div class="">
							<div class="text-center">
								<h3><a class="text-decoration-none text-warning">'.$entry["name"].'</a></h3>
								<h5>'.$dateText.'</h5>
								<h5>'.$placeText.'</h5>
								<div class="text-center userMess">'.$entry["description"].'</div>

							</div>
						</div>
											
					</div>
					<div class="col-lg-6 float-right">
						<img src="'.$entry['picture_url'].'" alt="avatar" class="rounded" style="width: 100%; height: auto; min-width: 100px">
					</div>
				</div>
			</div>									
			';
		}
		
		if($tableName == 'infos') {
			echo 
			'
			<div class="row my-5" data-aos="zoom-out-up" data-aos-duration="1000" data-aos-easing="linear">
				<div class="border-bottom rounded border-warning infos p-lg-3 p-1 col-md-6 offset-md-3">
					<div class="d-none d-md-flex p-0 m-0 row w-100">
						<div class="text-left w-75">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>
						<div class="date text-right w-25">'.changeTimeOrder($activity["time"]).'</div>
					</div>
					
						<div class="d-block d-md-none p-0 m-0 row w-100">
						<div class="text-left w-100">Użytkownik '.$owner['name']." ".$owner["surname"].' dodał/a wpis</div>

						<div class="date text-right w-100">'.changeTimeOrder($activity["time"]).'</div>
					</div>
					
					<div class="mb-5 mt-1">
						<div class="text-center"><h5>Kategoria: Ogłoszenie</h5></div>					

					</div>	
					<div class="col-lg-6 float-left">										
						<div class="">
							<div class="text-center">
								<h3><a class="text-decoration-none text-warning">'.$entry["title"].'</a></h3>
								<div class="text-center userMess">'.$entry["message"].'</div>

							</div>
						</div>
											
					</div>
					<div class="col-lg-6 float-right">
						<img src="'.$entry['picture_url'].'" alt="avatar" class="rounded" style="width: 100%; height: auto; min-width: 100px">
					</div>
				</div>
			</div>									
			';
		}
				
			
	}
	?>
			
			</div>
			

