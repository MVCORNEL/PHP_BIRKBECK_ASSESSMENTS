<?php 
require 'models/forms.php';
require 'models/validators.php';
$isLogOut=isset($_POST[LogoutForm::LOGOUT]);
$fields=Form::FORM_FIELDS;
#where the POST global array will be submitted
$self=htmlentities($_SERVER['PHP_SELF']);

#LOGOUT TIGGERED
if($isLogOut){
	destroySession();
	reloadCurrentPage();
}
//DISPLAY SIGN OUT
if(isset($_SESSION['uname'])){
	$logoutForm=new LogoutForm($self,$_SESSION,$fields);
	$logoutForm->displayForm(); 
}	
#JUST AN EXTRA SAVE CONDITION
else{
	redirectToHomePage();
}

?>