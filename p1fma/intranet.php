<!DOCTYPE html>
<?php 
require 'modules/sessions.php';
startSession();
denyNoUserAccess();
?>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Intranet</title>
		<link rel="stylesheet" href="styles/style.css">
</head>
<body>
	<?php
	include 'forms/logout.php';
	require 'modules/nav.php';
	showNav(2);
	include 'modules/intranet_links.php';
	?>	


</body>
</html> 