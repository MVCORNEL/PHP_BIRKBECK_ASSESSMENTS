 <?php
 //function used to start a session
 function startSession()
 {
     session_start();
 }
 #function used to reload the current page
 function reloadCurrentPage()
 {
     header('Location: ' . $_SERVER['PHP_SELF']);
 }
 #function used to redirect to the specified page index.php
 function redirectToHomePage()
 {
     header("Location: index.php");
     exit();
 }

 #function used to destroy a session and all the cookies related with that
 function destroySession()
 {
     $_SESSION = [];
     if (ini_get("session.use_cookies")) {
         echo 'session.use_cookies';
         //clear superglobal array
         $_SESSION = [];
         $yesterDay = time() - 24 * 60 * 60;
         $params = session_get_cookie_params();
         setcookie(session_name(), '', $yesterDay, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
     }
     session_destroy();
 }
 
 #function used to check a user is login to the current session by checking if the global SESSION array has set any element
 #@return true if the current session has an element with the key admin, and false othwise
 function isUser()
 {
     if (isset($_SESSION['admin'])) {
         return true;
     }
     return false;
 }
 #function used to redirect to the home page if the current user is not logged in
 function denyNoUserAccess()
 {
     if (!isUser()) {
         redirectToHomePage();
     }
 }
 
 #function used to redirect the user to the home page if the current user is not an admin 
 function denyNoAdminAccess()
 {
     if (isUser()) {
         if ($_SESSION['admin'] != 1) {
             redirectToHomePage();
         }
     } else {
         redirectToHomePage();
     }
 }
