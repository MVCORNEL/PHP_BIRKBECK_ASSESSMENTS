<?php
#abstract class used to hold and encapsulate the common behaviour between the [LoginValidator] and  [SignupValidator]
#the class is abstract because there is no need as this class to be ever instantiated
#all the protected field and methods can be accessed only in the scope of this class or the child classes
#this class functionality is simple, after the formValidate methos will be called all the valid and invalid inputs will be store into lists, being ready for its use
abstract class Validator{
	const USERS_TXT_FILE="users.txt";
	#fields that will be injected from the from class, into the current class
	protected $FORM_FIELD=[];
	protected $postedData = [];
    protected $cleanData = [];
    protected $errorData = [];
	#list that will hold all the user details after the user existance will be checked
	protected $userDetails = [];
	
	#constructor used for child class instantiation and to injects data into the current class through its parameters. 
	#@postData -> expects a list of elements, more specific POST data global array after a form submission
	#@formFields -> expects a list of elements, more specific a list that contains the name of all fields that need to be validated within a form
	public function __construct($postData,$formFields){
        $this->postedData = $postData;
		$this->FORM_FIELD = $formFields;
    }
	
	#function that must be implemented by the child classes
    abstract function validateForm();
	
	#function used to valdiate a username field, if the user is valid its data will be added to the clean data list oterwise to the error data list
    protected function validateUserName(){
        $uname = htmlentities(trim($this->postedData[$this->FORM_FIELD['UNAME']]));
        //EMPTY
        if (empty($uname)) {
            $this->addError($this->FORM_FIELD['UNAME'], 'empty username');
            return;
        }
		//ALPHANUMRIC
	    if (!ctype_alnum ($uname) ) {
            $this->addError($this->FORM_FIELD['UNAME'], 'invalid user name, expects only alphanumeric characters');
            return;
        }
		//RANGE
	    if (strlen ($uname)>=16 ) {
            $this->addError($this->FORM_FIELD['UNAME'], 'invalid user name,expects maximum than 16 charactes');
            return;
        }
        //CLEAN
        $this->addCleanData($this->FORM_FIELD['UNAME'], $uname);
    }
	
	#function used to valdiate a password field, if the password is valid its data will be added to the clean data list oterwise to the error data list
    protected function validatePassword(){
        $password = htmlentities(trim($this->postedData[$this->FORM_FIELD['PASSWORD']]));
        //EMPTY
        if (empty($password)) {
            $this->addError($this->FORM_FIELD['PASSWORD'], 'empty password');
            return;
        }
		//ALPHANUMRIC
	    if (!ctype_alnum ($password) ) {
            $this->addError($this->FORM_FIELD['PASSWORD'], 'invalid password, expects only alphanumeric characters');
            return;
        }
		//RANGE
	    if (strlen ($password)>=16 ) {
            $this->addError($this->FORM_FIELD['PASSWORD'], 'invalid password,expects maximum than 16 charactes');
            return;
        }
        //CLEAN
        $this->addCleanData($this->FORM_FIELD['PASSWORD'], $password);
    }
	
	#function used to check if a form validated by simply cheching if the errorList where all the errors will be store is empty
	#@returns true if the is no error store in the list and false oterwise
	public function isValid(){
        return empty($this->errorData);
    }

	#function is a simple class getter, returns the [errorData] list
	#@returns a list of errors
    public function getErrors(){
        return $this->errorData;
    }
	
	#function is a simple class getter, returns the [cleanData] list
	#@returns a list of clean data
    public function getCleanData(){
        return $this->cleanData;
    }
	
	#function is a simple class getter, returns the [errorData] list
	#@returns a list of errors
    public function getUserDetails(){
        return $this->userDetails;
    }

	#function adds errors to to a error list based on key(field name) and its value(its invalid data)
    protected function addError($key, $val){
        $this->errorData[$key] = $val;
    }
	
	#function adds clean data to to a clean data list based on a key(field name) and its value(its validated data)
    protected function addCleanData($key, $val){
        $this->cleanData[$key] = $val;
    }
	
	protected function addUserDetails($key, $val){
		 $this->userDetails[$key] = $val;
	}
	
	 #function used to validate retrive the user infomartion in case that this exists
	 protected function doesUserExist(){
        if (file_exists(self::USERS_TXT_FILE)) {
            #open file stream
            if ($handle = fopen(self::USERS_TXT_FILE, 'r')) {
                while (!feof($handle)) {
                    //remove white space before and after each line and store each line data into an array
                    $rawData[] = trim(fgets($handle));
                }
                #the user name that we want to save
                $currentUser = $this->cleanData[$this->FORM_FIELD['UNAME']];
                for ($i = 0; $i < sizeOf($rawData); $i++) {
					$userData=explode(":", $rawData[$i]);
					#word before the first colun will be the user name for each raw data line
                    $user = $userData[0];
					#if the user exists 
				    if ($user === $currentUser) {
					    #store the user details for further validation and data process
						#password fields will be omitted
					    $this->addUserDetails($this->FORM_FIELD['UNAME'],$userData[0]);	
						$this->addUserDetails($this->FORM_FIELD['PASSWORD'],$userData[1]);						
						$this->addUserDetails($this->FORM_FIELD['TITLE'],$userData[2]);
						$this->addUserDetails($this->FORM_FIELD['FNAME'],$userData[3]);
						$this->addUserDetails($this->FORM_FIELD['SURNAME'],$userData[4]);
						$this->addUserDetails($this->FORM_FIELD['EMAIL'],$userData[5]);
						$this->addUserDetails($this->FORM_FIELD['ADMIN'],$userData[7]);		
					    fclose($handle);
						return true;
                    }
                }	
				fclose($handle);
            }
        }		
		#user doesn't exist
		return false;
	}
}
#Class used to encapsulate the login validator behaviour.This class extends from Validator inheriting all its behaviour and state
#so parent methods and varaibles can pe accessed by using the parent:: expression
#this class will implement the validateForm method
class LoginValidator extends Validator{  
		
	#function used to validate a sign in form by calling validateUserName and validatePassword parent methods
	#if the username and password are valid they will be further check if they match with any user data that is stored in a txt file
    public function validateForm(){
        parent::validateUsername();
        parent::validatePassword();
		//the user name and password are valid, the next step is to check if the user is valid
		if(parent::isValid()){
			$this->doesUserMatchPassword();
		}
    }
	
	#function used to validate a username existance into a text file list.If the user doesn't exist add the corresponding error the the error list
	 private function doesUserMatchPassword(){
		#user exist-
		if(parent::doesUserExist()==true){
			#check if the password taken from clean data
			$currentPassword=$this->cleanData[$this->FORM_FIELD['PASSWORD']];
			#user password will always be found as the second element on the user data taken from the text file
			$userPassword=$this->userDetails[$this->FORM_FIELD['PASSWORD']];
			#remove the password from the userDetails list because won't be used anywhere else
			unset($this->userDetails[$this->FORM_FIELD['PASSWORD']]); 
			#password and the username match
			if($currentPassword==$userPassword){
				return true;
			}
			#pusername exists but the passowrd doesn't match it
			else{
				parent::addCleanData($this->FORM_FIELD['PASSWORD'], "");
				parent::addError($this->FORM_FIELD['PASSWORD'], "password doesn't match the username");
				return false;
			}
		}
		#user doesn't exists
		else{
			#clean user field from clean data list because the user already exists
			parent::addCleanData($this->FORM_FIELD['UNAME'], "");
			parent::addError($this->FORM_FIELD['UNAME'], "username doesn't exist");
			return false;
		}
    }
}
#class used to encapsulate the login validator behaviour.This class extends from Validator inheriting all its behaviour and state
#so parent methods and varaibles can pe accessed by using the parent:: expression
#this class will implement the validateForm method
class SignupValidator extends Validator{  

	#function used to validate a sign up form by calling all the required validation methods
	#if the username and password are valid they will be further check if they match with any user data that is stored in a txt file
    public function validateForm(){
		//parent class methods used to validate username and password input
		parent::validateUsername();
        parent::validatePassword();
        $this->validateTitle();
        $this->validateFirstName();
        $this->validateSurname();
        $this->validateEmail();
        $this->validateRetypedPassword();
		$this->validateAdmin();
		if(parent::isValid()){
			if($this->doesUserExist()){
				//if the user exists
				parent::addCleanData($this->FORM_FIELD['UNAME'], "");
				parent::addError($this->FORM_FIELD['UNAME'], "username already exists exist");
			}
		}
    }

	#function used to valdiate a value from a dropdown box.If is valid add it to clean list oterwise add the error to the error list
    private function validateTitle(){
        define('TITLE_OPTIONS', ['Mr', 'Miss', 'Ms', 'Mx', 'Sir', 'Dr']);
		//ESCAPE HTML CHARACTERS AND REMOVE UNWANTED WHITE SPACES AT THE START AND END OF THE INPUT
        $title = htmlentities(trim($this->postedData[$this->FORM_FIELD['TITLE']]));
		//EMPTY TITLE
        if (empty($title)) {
            parent::addError($this->FORM_FIELD['TITLE'], 'Please select a value.');
            return;
        }
        //INVALID OPTION
        if (!in_array($title, TITLE_OPTIONS)) {
            parent::addError($this->FORM_FIELD['TITLE'], "Invalid title option");
            return;
        }
        //CLEAN
        parent::addCleanData($this->FORM_FIELD['TITLE'], $title);
    }
	
	#function used to valdiate a firstname field.If is valid add it to clean list oterwise add the error to the error list
    private function validateFirstName(){
		//ESCAPE HTML CHARACTERS AND REMOVE UNWANTED WHITE SPACES 
        $firstName = htmlentities(trim($this->postedData[$this->FORM_FIELD['FNAME']]));
        //EMPTY
        if (empty($firstName)) {
            parent::addError($this->FORM_FIELD['FNAME'], 'empty first name');
            return;
        }
		//ALPHABETIC 
	    if (!ctype_alpha ($firstName) ) {
            $this->addError($this->FORM_FIELD['FNAME'], 'invalid input, expects only alphabetic characters');
            return;
        }
		//Range 
	    if (strlen ($firstName)>=20 ) {
            $this->addError($this->FORM_FIELD['FNAME'], 'invalid input,expects maximum than 20 charactes');
            return;
        }
        //CLEAN
        parent::addCleanData($this->FORM_FIELD['FNAME'], $firstName);
    }
	
	#function used to valdiate a surname field.If is valid add it to clean list oterwise add the error to the error list
    private function validateSurname(){
		//ESCAPE HTML CHARACTERS AND REMOVE UNWANTED WHITE SPACES 
        $surname = htmlentities(trim($this->postedData[$this->FORM_FIELD['SURNAME']]));
        //EMPTY
        if (empty($surname)) {
            parent::addError($this->FORM_FIELD['SURNAME'], 'empty surname');
            return;
        }
		//ALPHABETIC 
	    if (!ctype_alpha ($surname) ) {
            $this->addError($this->FORM_FIELD['SURNAME'], 'invalid input, expects only alphabetic characters');
            return;
        }
		//Range 
	    if (strlen ($surname)>=20 ) {
            $this->addError($this->FORM_FIELD['SURNAME'], 'invalid input,expects maximum than 20 charactes');
            return;
        }
        //CLEAN
        parent::addCleanData($this->FORM_FIELD['SURNAME'], $surname);
    }
	
	#function used to valdiate a password field.If is valid add it to clean list oterwise add the error to the error list
    private function validateEmail(){
		//ESCAPE HTML CHARACTERS AND REMOVE UNWANTED WHITE SPACES 
        $email = htmlentities(trim($this->postedData[$this->FORM_FIELD['EMAIL']]));
        //EMPTY
        if (empty($email)) {
            parent::addError($this->FORM_FIELD['EMAIL'], 'empty email');
            return;
        }
        //INVALID
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            parent::addError($this->FORM_FIELD['EMAIL'], "Invalid email address");
            return;
        }
        //CLEAN
        parent::addCleanData($this->FORM_FIELD['EMAIL'], $email);
    }
	
	#function used to valdiate a retyped password field against the password field.If valid add it to clean list oterwise add the error to the error list
    private function validateRetypedPassword(){
        $password = htmlentities(trim($this->postedData[$this->FORM_FIELD['PASSWORD']]));
        $retypedPassword = htmlentities(trim($this->postedData[$this->FORM_FIELD['RETYPED_PASSWORD']]));
        //EMPTY
        if (empty($retypedPassword)) {
            parent::addError($this->FORM_FIELD['RETYPED_PASSWORD'], 'empty retyped password');
            return;
        }
        //INVALID
        if ($password !== $retypedPassword) {
            parent::addError($this->FORM_FIELD['RETYPED_PASSWORD'], "Retyped password doesn't match");
            return;
        }
        //CLEAN
        parent::addCleanData($this->FORM_FIELD['RETYPED_PASSWORD'], $retypedPassword);
    }
	
	#function used to check if a user is admin or not, if the user is admin return true otherwise false
	private function validateAdmin(){
		$isAdmin=false;
		if(isSet($this->postedData[$this->FORM_FIELD["ADMIN"]])){
		 $isAdmin=true;
		}
		  parent::addCleanData($this->FORM_FIELD['ADMIN'], $isAdmin);
	}
	
	#function used to create a user data.Each user data field will be delimited by a colun and concatenated as a whole. Example -> username:password......:isAdmin
	#@returns a String
    private function createUserData()
    {
        $userData = "";
        foreach ($this->FORM_FIELD as $key => $val) {
            $fieldData = $this->cleanData[$val];
            //USERNAME will be the first element so won't be preceded by colon
            if ($val === $this->FORM_FIELD['UNAME']) {
                $userData .= $fieldData;
                continue;
            }
            $userData .= ":" . $fieldData;
        }
        return $userData;
    }
	
	#function used in order to create a new user and save it into given text file. If there is not user with the same username the user will be saved into the text file.
    function trySaveUser()
    {
			#call the method resposible for mergeing all the user data into a single line 
            $user = $this->createUserData() . "\n";
			#user successfully created
            if (file_put_contents(self::USERS_TXT_FILE, $user, FILE_APPEND) !== false) {
                echo "<p>User successfully created with the following information: </p><br>";
				//display newly added user info
				foreach ($this->cleanData as $key=>$value) {
					echo ("$key : $value<br>");
				}
            }
			else{
				 echo "<p>Problem ocurred trying to create the new user </p><br>";
			} 
    }
}
?>