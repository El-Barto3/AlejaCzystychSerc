<?php
include 'database.php';

$ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

$query = $db->prepare("SELECT entry_key FROM activity WHERE hidden <> 0 AND table_name = 'book'");
$query->execute();
$hiddenActivities = $query->fetchAll();

$qry = $db->prepare("SELECT * FROM book");
$qry->execute();

$bookEntries = $qry->fetchAll();
?>
<div class="row">
<div class="col-sm-12 aktualnosciNaglowek border-bottom border-warning"><h1>Księga dusz</h1></div>
</div>
<div class="row">
<div class="col-sm-12 border-warning mb-3 mt-3 mx-0 px-0 pb-1 pt-1">
<table id="book" class="table row-border text-light text-break">
    <thead class="" style="background: #292b2c; color: #ffc107" >
        <tr>
            <th scope="col" class="d-none d-sm-table-cell">#</th>
            <th scope="col">Imię</th>
			<th scope="col">Nazwisko</th>
			<th scope="col" class="d-none d-sm-table-cell">Data urodzenia</th>
			<th scope="col">Data śmierci</th>
        </tr>
    </thead>
    <tbody>
	<?php
		$entryNumber = 1;
		foreach($bookEntries as $entry) {
			$hidden = false;
			foreach($hiddenActivities as $hiddenAct)
			{
				if($entry['entry_key'] == $hiddenAct['entry_key'])
					$hidden = true;
			}
			if(!$hidden)
			{
				echo
				'<tr class="tabela" style="cursor: pointer;" data-id="'.$entry["ID"].'">

					<td class="d-none d-sm-table-cell">'.$entryNumber.'</td>
					<td>'.$entry["name"].'</td>
					<td>'.$entry["lastname"].'</td>
					<td class="d-none d-sm-table-cell dat-id">'.$entry["birthday"].'</td>
					<td class="dat-id">'.$entry["deathday"].'</td>

				</tr>';
				$entryNumber = $entryNumber + 1;
			}	
		}
	?>
    </tbody>
</table>
</div>
</div>
<script type="text/javascript">
const polishData = () => {
  var number = $('.dat-id').length;
  for (var i = 0; i < number; i++) {
    var value = $('.dat-id').eq(i).html();
    var x = value.substr(0, 4);
    value = value.slice(5, 10);
    value = value + "-" + x;
    if ($('.dat-id').eq(i).html() != "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-") $('.dat-id').eq(i).html(value);
  }
}

document.querySelectorAll("table tr").forEach((e, i) => {
   if(e.dataset["id"]) {
       e.addEventListener("click", () => { document.location.href = "?a=person&id=" + e.dataset["id"]; });
    }
});



$('#myTable').DataTable( {
    buttons: {
        buttons: [
            { extend: 'previous', className: 'previousButton' },
            { extend: 'active', className: 'activeButton' }
        ]
    }
} );

$(document).ready(function() {
  $('#book').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.22/i18n/Polish.json'
    }
  });
  polishData();
  $(".sorting").click(function(){polishData();});
  $(".sorting_desc").click(function(){polishData();});
  $(".sorting_asc").click(function(){polishData();});
  $('#book_previous a').click(function(){polishData();});
  $('#book_next a').click(function(){polishData();});
});</script>