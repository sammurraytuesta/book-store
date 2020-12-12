<?php

// database functions ************************************************

function fCleanString($link, $UserInput, $MaxLen) {
   //Escape special characters - very important.
   //mysqli_real_escape_string requires database connection
$UserInput = mysqli_real_escape_string($link, $UserInput);

   //tidy up and truncate to max length.
$UserInput = strip_tags($UserInput);
$UserInput = trim($UserInput);
return substr($UserInput, 0, $MaxLen);
}

function fCleanNumber($UserInput) {
$pattern = "/[^0-9\.]/";  //remove everything except 0-9 and period
$UserInput = preg_replace($pattern, "", $UserInput);
return substr($UserInput, 0, 8);
}

function fConnectToDatabase() {
   //For code reusability this function is often located in its own file.
   //Pages that require database assess include it with include('connection.php');
   //where 'connection.php' is the name of your connect file.
   //Create a connection object
   //@ suppresses errors.  
   //parameters: mysqli_connect('my_server', 'my_user', 'my_password', 'my_db');  
$link = @mysqli_connect('yorktown.cbe.wwu.edu', 'murray39', 'themostsecurepasswordever', 'murray39');

   //handle connection errors
if (!$link) {
    die('Connection Error: ' . mysqli_connect_error());
}
return $link;
}