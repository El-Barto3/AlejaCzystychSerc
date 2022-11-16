<?php
include 'database.php';

$ID = getVariable("id");

if($ID != "") {
	$qry = $db->prepare("SELECT * FROM book WHERE ID = :ID AND display = 1");
	$qry->bindParam(":ID", $ID);
	$qry->execute();
	
	$entry = $qry->fetch();
}

if($entry["grave_url"] == "") {
?>
                            <div class="row">
                                <div class="col-sm-12 aktualnosciNaglowek border-bottom border-warning"><h1>Spacer wirtualny</h1></div>
                            </div>
                            <div class="row">
                                   <div class="trescSrodkowaSpacer">
                                    <!-- W tym divie maja byc wiadomosci-->
                                    <a href="https://bartoporfolio.pl/alejaserc/spacer" target="_blank" class="facebookKontakt text-light menuOpcja text-decoration-none" style="text-align: center;">
                                        Kliknij tutaj, aby otworzyć spacer w osobnej karcie.
                                    </a>
                                    <iframe src="https://bartoporfolio.pl/alejaserc/spacer" id="spacer" class="spacer mt-3"></iframe>
                                </div>
                            </div>
							
							<div class="row mx-auto ">
															</div>

<?php
}
else {
?>
                            <div class="row">
                                <div class="col-sm-12 aktualnosciNaglowek border-bottom border-warning"><h1>Spacer wirtualny</h1></div>
                            </div>
                            <div class="row">
                                   <div class="trescSrodkowaSpacer">
                                    <!-- W tym divie maja byc wiadomosci-->
                                    <a href="<?=$pageMainURL?>/spacer/haha" target="_blank" class="facebookKontakt text-light menuOpcja" style="text-align: center;">
                                        Kliknij tutaj, aby otworzyć spacer w osobnej karcie.
                                    </a>
                                    <iframe src="<?=$entry["grave_url"]?>" class="spacer mt-3"></iframe>
                                </div>
                            </div>
<?php
}
?>
<script>
const sendToImage = (rnd) => {
  if(!$(".owl-stage", document.getElementById('spacer').contentDocument).eq(0).length) setTimeout(function() {
    sendToImage(rnd);
  }, 50);
  if(rnd == 0) return;
  var div = document.createElement('div');
  div.classList.add("item");
  div.setAttribute('data-rnd', rnd);
  div.id = "imgGo";
  document.getElementById('spacer').contentDocument.getElementsByClassName("owl-stage")[0].appendChild(div);
  document.getElementById('spacer').contentDocument.getElementById('imgGo').click();
  document.getElementById('spacer').contentDocument.getElementById('imgGo').remove();
}
</script>
<?php
  if(isset($_POST['rnd']))
  {
     $myRnd = $_POST['rnd'];
     echo "<script>sendToImage(\"$myRnd\")</script>";
  }
?>