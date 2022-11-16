<?php 
	session_start();

	unset($_SESSION['logged']);
	unset($_SESSION['userID']);
	unset($_SESSION['loggedFB']);
	
	session_destroy();
	
?>
<script type="text/javascript">window.location.href = '?a=news';</script>