<?php 
require 'models/forms.php';
require 'models/validators.php';
//Check if the form inputs are submited
$isSubmitted=isset($_POST[Form::SUBMIT]);
$isLogOut=isset($_POST[LogoutForm::LOGOUT]);
$fields=Form::FORM_FIELDS;
$cleanData=[];
$errorData=[];
$isValid=false;
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

//SUBBMIT
if($isSubmitted){
	$validator=new SignupValidator($_POST,$fields);
	$validator->validateForm();	
	$cleanData=$validator->getCleanData();
	$errorData=$validator->getErrors();
	$isValid=$validator -> isValid();	
}
//VALID DATA TRY CREATE ACCOUNT
if($isSubmitted && $isValid){
	$validator->trySaveUser(); 	
}
//DISPLAY REDISPLAY FORM
else{
	$signForm=new SignupForm($self,$cleanData,$errorData);
	$signForm->displayForm();
 } 
?>