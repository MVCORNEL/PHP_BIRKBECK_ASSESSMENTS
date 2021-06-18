<!DOCTYPE html>
<?php 
require 'modules/sessions.php';
startSession();
denyNoAdminAccess();
?>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>BBK ITApps - Web Programming using PHP</title>
		<link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php
require 'modules/nav.php';
showNav(3);
include 'forms/signup.php' ?>
</body>
</html> 