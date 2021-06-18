<?php
#interface that will enforce all the concrete class that implments IForm to implement its methods
interface IForm
{
    function displayForm();
}
#this class implements
#abstract class used to hold and encapsulate the common state and behaviour that will be shared between the child classes Signin and Login
#abstract because there is not need as this class to be ever instantiated. It will be used just as a shared data container
#protected functions and fields within this class will be accessible(visible) only for the class itself and for the child classes, providing extra security
#the abstract function is a function that must be implemented by all the child classes, that extend this class
abstract class Form implements IForm
{
    const FORM_FIELDS = [
        'UNAME' => 'uname',
        'PASSWORD' => 'pass',
        'TITLE' => 'utitle',
        'FNAME' => 'fname',
        'SURNAME' => 'sname',
        'EMAIL' => 'email',
        'RETYPED_PASSWORD' => 'r_pass',
        'ADMIN' => 'admin',
    ];
    const SUBMIT = 'submit_state';
    protected $self = '';
    protected $cleanData = [];
    protected $errorData = [];
    #constructor used for child class instantiation and to injects data into the current class through its parameters.
    #@param $s -> expects the url that defines where the form POST global array will be available, when the form will be submitted
    #@param $clean -> expects all the clean data that the form will be refilled with
    #@param $error -> expects all the errors representing invalid data that the form will display into its span elements
    function __construct($s, $clean, $error)
    {
        #assignin values into the following class members
        $this->self = $s;
        $this->cleanData = $clean;
        $this->errorData = $error;
    }
    #function used to create a warning message that will announce the user that the form submited has faulty inputs(invalid)
    #this message will be shown only if the $errorData list is not empty
    protected function showErrorFlag()
    {
        if (!empty($this->errorData)) {
            echo '<h1> Please correct the flagged fields!</h1>';
        }
    }
    #function used to create a field value attribute based on the clean data list element,
    #@param -> a String element that will be used a key in order to retriev data form the list
    #@returns -> a String value represting a html form->input element's value attribute
    protected function showValue($field)
    {
        if (isset($this->cleanData[$field])) {
            return " value= '" . $this->cleanData[$field] . "' ";
        }
    }
    #function used to create a span node with an paragraph that contains the error. The error will be extract from the error list based on the field name
    #@param -> a String element that will be used a key in order to retriev data form the list
    protected function showError($error)
    {
        if (isset($this->errorData[$error])) {
            return " 
			<span><p>" .
                $this->errorData[$error] .
                " </p></span>";
        }
    }
    #function that must be implemented by the child classes
    abstract function displayForm();
}
#Class resposible for display the SignupForm form based on clean and error lists.Inherits all parent [Form] functions and fields
class SignupForm extends Form
{
    #function checks if @title taken as parameter matches with the element of an list with the key of title, if it does returns selected oterwise nothin
    #@param expects a String representing a dropdown box option
    #@returns a String with the value ' selected' if the parameter matches with the element value from the clean data list, and an empty String otherwise
    private function isSelected($title)
    {
        if (isset($this->cleanData[self::FORM_FIELDS['TITLE']])) {
            if ($this->cleanData[self::FORM_FIELDS['TITLE']] === $title) {
                return " selected";
            }
        }
        return '';
    }
    #function used to display the singup form with the following fields (title,first name, surname, email, username, password, retype passsowrd and isAdmin)
    #the form inputs and error shown are directly depented by the cleanData and erorData list.
    public function displayForm()
    {
        #announce the user that errors exist by calling the parent method showErrorFlag
        parent::showErrorFlag();
        echo "
		<div id='form-container'>
			<form action='" .
            $this->self .
            "' method='post'>
				<fieldset>
					<legend>Sign Up</legend>
					<div>
						<label for='title'>Title</label>
						<select id='title' name=" .
            self::FORM_FIELDS['TITLE'] .
            ">
							<option value='Mr'" .
            self::isSelected('Mr') .
            ">Mr</option>
							<option value='Miss'" .
            self::isSelected('Miss') .
            ">Miss</option>
							<option value='Ms' " .
            self::isSelected('Ms') .
            ">Ms</option>
							<option value='Mx'" .
            self::isSelected('Mx') .
            ">Mx</option>
							<option value='Sir'" .
            self::isSelected('Sir') .
            ">Sir</option>
							<option value='Dr'" .
            self::isSelected('Dr') .
            ">Dr</option>
						</select>
					</div>	
					<div>
	
						<label for='first_name'>*First Name(must contain alphabetic characters,min 1 character maximum  20)</label>
						<input type='text' name=" .
            self::FORM_FIELDS['FNAME'] .
            " id='first_name' " .
            parent::showValue(self::FORM_FIELDS['FNAME']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['FNAME']) .
            "
					</div>
					<div>
						<label for='surname'>*Surname (must contain alphabetic characters,min 1 character maximum  20)</label>
						<input type='text' name=" .
            self::FORM_FIELDS['SURNAME'] .
            " id='surname' " .
            parent::showValue(self::FORM_FIELDS['SURNAME']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['SURNAME']) .
            "
					</div>
					<div>
						<label for='email_address'>*Email</label>
						<input type='text' name=" .
            self::FORM_FIELDS['EMAIL'] .
            " id='email_address' " .
            parent::showValue(self::FORM_FIELDS['EMAIL']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['EMAIL']) .
            "
					</div>
					<div>
						<label for='user'>*Username (must contain alphanumeric characters,min 1 character maximum  16)</label>
						<input type='text' name=" .
            self::FORM_FIELDS['UNAME'] .
            " id='user' " .
            parent::showValue(self::FORM_FIELDS['UNAME']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['UNAME']) .
            "
					</div>
					<div>
						<label for='pwd'>*Password (must contain alphanumeric characters,min 1 character maximum  16) </label>
						<input type='password' name=" .
            self::FORM_FIELDS['PASSWORD'] .
            " id='pwd' " .
            parent::showValue(self::FORM_FIELDS['PASSWORD']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['PASSWORD']) .
            "
					</div>
					<div>
						<label for='retyped_pwd'>*Retyped Password (must match the password)</label>
						<input type='password' name=" .
            self::FORM_FIELDS['RETYPED_PASSWORD'] .
            " id='retyped_pwd' " .
            parent::showValue(self::FORM_FIELDS['RETYPED_PASSWORD']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['RETYPED_PASSWORD']) .
            "
					</div>			
					<div>
						<input type='checkbox' id='showpasswords' name=" .
            self::FORM_FIELDS['ADMIN'] .
            " value='1' />
						<label for='showpasswords'>Admin</label>
					</div>
					<div>
						<input type='submit' name='" .
            self::SUBMIT .
            "' value='submit' />
					</div>
				</fieldset>
			</form>
		</div>";
    }
}
#Class resposible for display the LoginForm form based on clean and error lists.Inherits all parent [Form] functions and fields
class LoginForm extends Form
{
    #function used to display a login form that contains a userfield and a password
    #the form inputs and error shown are directly depented by the cleanData and erorData list.
    function displayForm()
    {
        #announce the user that errors exist by calling the parent method
        parent::showErrorFlag();
        echo "
		<div id='form-login'>
			<form action='" .
            $this->self .
            "' method='post'>
				<fieldset>
					<legend>Login</legend>	
					<div>
						<label for='user'>*Username (must contain alphanumeric characters,min 1 character maximum  16)</label>
						<input type='text' name=" .
            self::FORM_FIELDS['UNAME'] .
            " id='user'" .
            parent::showValue(self::FORM_FIELDS['UNAME']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['UNAME']) .
            "
					</div>
					<div>
						<label for='pwd'>*Password (must contain alphanumeric characters,min 1 character maximum  16)</label>
						<input type='password' name=" .
            self::FORM_FIELDS['PASSWORD'] .
            " id='pwd' " .
            parent::showValue(self::FORM_FIELDS['PASSWORD']) .
            " />
						" .
            parent::showError(self::FORM_FIELDS['PASSWORD']) .
            "
					</div>
					<div>
						<input type='submit' name=" .
            self::SUBMIT .
            " value='login' />
					</div>
				</fieldset>
			</form>
		</div>";
    }
}
#Class resposible for display the LogoutForm .Implements IForm
class LogoutForm implements IForm
{
    private $self = '';
    private $sessionList = '';
    private $FORM_FIELDS = [];
    const LOGOUT = 'logout_state';
    #constructor used for class instantiation and to injects data into the current class through its parameters.
    #@param $s -> expects the url that defines where the form POST global array will be available, when the form will be submitted
    #@param $uname ->expects a striing,more precise a user name
    function __construct($s, $session, $field)
    {
        $this->self = $s;
        $this->sessionList = $session;
        $this->FORM_FIELDS = $field;
    }

    #function used to return a field value retrieved from the session list
	#@param - $sessionKey expects a session key, more specific a form element name
	#@return a String
    private function showUserDetail($sessionKey)
    {
        if (isset($this->sessionList[$sessionKey])) {
            return $this->sessionList[$sessionKey];
        }
        return '';
    }
    #function used to return a string value based on the value of the element with the key  admin from the session list
	#returns Admin if the session list element with the key admin is 1 and Tutor oterwise
    private function showIsAdmin()
    {
        if (isset($this->sessionList[$this->FORM_FIELDS['ADMIN']])) {
            if ($this->sessionList[$this->FORM_FIELDS['ADMIN']] == 1) {
                return "Admin";
            }
            return "Tutor";
        }
    }
    #function used to display a logout form
    public function displayForm()
    {
        echo "<div id='form-out'>
				<form action='" .
            $this->self .
            "' method='post'>

				<fieldset>
					<legend>Logout</legend>
				<div>
					<p>Welcome:" .
            $this->showUserDetail($this->FORM_FIELDS['TITLE']) .
            ". " .
            $this->showUserDetail($this->FORM_FIELDS['SURNAME']) .
            " " .
            $this->showUserDetail($this->FORM_FIELDS['FNAME']) .
            " </p>
					<p>User: " .
            $this->showUserDetail($this->FORM_FIELDS['UNAME']) .
            " </p>
					<p>Account type: " .
            $this->showIsAdmin() .
            " </p>
				</div>
					<div>
						<input type='submit' name='" .
            self::LOGOUT .
            "' value='logout' />
					</div>
				</fieldset>
			</form>
		</div>";
    }
}
?>
