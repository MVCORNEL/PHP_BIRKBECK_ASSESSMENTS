<?php 
require 'models/forms.php';
require 'models/validators.php'; 
$isSubmitted=isset($_POST[Form::SUBMIT]);
$isLogOut=isset($_POST[LogoutForm::LOGOUT]);
$loginFields=Form::FORM_FIELDS;
#lists that will be populate with valid and invalid data on form submission
$cleanData=[];
$errorData=[];
#where the POST global array will be submitted
$self=htmlentities($_SERVER['PHP_SELF']);
#LOGOUT
if($isLogOut){
	destroySession();
	reloadCurrentPage();
}
//SUBMIT
if($isSubmitted){
	$validator=new LoginValidator($_POST,$loginFields);
	$validator->validateForm();	
	$cleanData=$validator->getCleanData();
	$errorData=$validator->getErrors();
}
//VALIDATION SUCCESSFUL NOW TRY TO LOG IN
if($isSubmitted && empty($errorData)){
	//LOGIN
	$userDetails=$validator->getUserDetails();	
	$_SESSION[$loginFields['UNAME']]=$userDetails[$loginFields['UNAME']];
	$_SESSION[$loginFields['TITLE']]=$userDetails[$loginFields['TITLE']];
	$_SESSION[$loginFields['FNAME']]=$userDetails[$loginFields['FNAME']];
	$_SESSION[$loginFields['SURNAME']]=$userDetails[$loginFields['SURNAME']];
	$_SESSION[$loginFields['EMAIL']]=$userDetails[$loginFields['EMAIL']];
	$_SESSION[$loginFields['ADMIN']]=$userDetails[$loginFields['ADMIN']];
	reloadCurrentPage();

}
else{
	//DISPLAY SIGN OUT
	if(isset($_SESSION['uname'])){
		$logoutForm=new LogoutForm($self,$_SESSION,$loginFields);
		$logoutForm->displayForm(); 
	}
	//DISPLAY SIGN IN FORM
	else{
		//VALIDATION SUCCESSFUL NOW TRY TO LOG IN
		$loginForm=new LoginForm($self,$cleanData,$errorData);
		$loginForm->displayForm(); 
	}	
}
?>