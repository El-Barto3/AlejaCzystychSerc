<?php
		session_start();
		
			include 'database.php';
			include 'globals.php';
			
			$page = isset($_GET["a"]) ? $_GET["a"] : "";
			
			function load($page) {
				include 'pages/'.$page.'.php';
				echo PHP_EOL;
			}
			
			if($page == "") {
				load('contact');
			}
			else
			{			
				$allowed = ["person", "book", "contact", "walk","entry", "entry_info", "entry_person", "entry_event", "entry_funeral", "entry_other", "admin", "news", "rules", "logout"];
			
				if(in_array($page, $allowed)) {
					load($page);
				}
				else {
					$page = "contact";
					load($page);
				}
			}
			
			
?>