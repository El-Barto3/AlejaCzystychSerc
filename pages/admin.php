<?php
include 'database.php';

$password = getVariable("password");
if($password != "w6EUPz4e6FyXLkAA") {
	header("Location: /?a=book");
	exit();
}

$qry = $db->prepare("SELECT * FROM book WHERE display = 0");
$qry->execute();
$bookEntries = $qry->fetchAll();

$qry = $db->prepare("SELECT * FROM comments WHERE display = 0");
$qry->execute();
$comments = $qry->fetchAll();

$id = getVariable("id");
$type = getVariable("type");
$option = getVariable("option");

if($id != "" && $type != "" && $option != "") {
	if($type == "comments") {
		$qry = $db->prepare("UPDATE comments SET display = :option WHERE ID = :ID");
		$qry->bindParam(":ID", $id);
		$qry->bindParam(":option", $option);
		$qry->execute();
	}
	else if($type == "book") {
		$qry = $db->prepare("UPDATE book SET display = :option WHERE ID = :ID");
		$qry->bindParam(":ID", $id);
		$qry->bindParam(":option", $option);
		$qry->execute();
	}
	
	header("Location: /?a=admin&password=".$password);
}
?>
<h3> Lista wpisów do weryfikacji </h3>
<table id="book">
    <thead>
        <tr>
            <th>ID</th>
            <th>Imię i nazwisko</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
	<?php
		foreach($bookEntries as $entry) {
			echo
			'<tr data-id="'.$entry["ID"].'">
				<td>'.$entry["ID"].'</td>
				<td>'.$entry["name"].'</td>
				<td>Zaakceptuj</td>
				<td>Odrzuć</td>
			</tr>';
		}
	?>
    </tbody>
</table>

<div class="break"></div>

<h3> Lista komentarzy do weryfikacji </h3>
<table id="comments">
    <thead>
        <tr>
            <th>ID</th>
            <th>Imię i nazwisko</th>
            <th>Komentarz</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
	<?php
		foreach($comments as $entry) {
			echo
			'<tr data-id="'.$entry["book_id"].'" data-comment="'.$entry["ID"].'">
				<td>'.$entry["ID"].'</td>
				<td>'.$entry["name"].'</td>
				<td>'.$entry["text"].'</td>
				<td>Zaakceptuj</td>
				<td>Odrzuć</td>
			</tr>';
		}
	?>
    </tbody>
</table>
<script type="text/javascript">
document.querySelectorAll("table tr").forEach((e, i) => {
	if(e.dataset["id"]) {
		e.querySelectorAll("td")[0].addEventListener("click", () => {
			document.location.href = "?a=person&id=" + e.dataset["id"];
		});
		e.querySelectorAll("td")[1].addEventListener("click", () => {
			document.location.href = "?a=person&id=" + e.dataset["id"];
		});
	}
});

document.querySelectorAll("#book tr").forEach((e, i) => {
	if(e.dataset["id"]) {
		e.querySelectorAll("td")[2].addEventListener("click", () => {
			document.location.href = "?a=admin&password=<?=$password?>&type=book&option=1&id=" + e.dataset["id"];
		});
		e.querySelectorAll("td")[3].addEventListener("click", () => {
			document.location.href = "?a=admin&password=<?=$password?>&type=book&option=-1&id=" + e.dataset["id"];
		});
	}
});

document.querySelectorAll("#comments tr").forEach((e, i) => {
	if(e.dataset["comment"]) {
		e.querySelectorAll("td")[3].addEventListener("click", () => {
			document.location.href = "?a=admin&password=<?=$password?>&type=comments&option=1&id=" + e.dataset["comment"];
		});
		e.querySelectorAll("td")[4].addEventListener("click", () => {
			document.location.href = "?a=admin&password=<?=$password?>&type=comments&option=-1&id=" + e.dataset["comment"];
		});
	}
});

$(document).ready(function () {
    $('#book').DataTable({
		language: {
			url: '//cdn.datatables.net/plug-ins/1.10.22/i18n/Polish.json'
		}
	});
	
	$('#comments').DataTable({
		language: {
			url: '//cdn.datatables.net/plug-ins/1.10.22/i18n/Polish.json'
		}
	});
});
</script>