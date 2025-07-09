<?php
session_start(); // Start the session to access session variables

// Destroy the session to log the user out
session_unset();  // Removes all session variables
session_destroy(); // Destroys the session

// Redirect the user to the login page or homepage
header("Location: login.php");
exit();
