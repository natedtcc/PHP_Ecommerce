<?php # logout.php = N. Nasteff

// This script will log the user out, destroy the session, and clear the cookie.
// Then it will redirect the user back to the site index
// NOTE - THE LINK TO THIS WILL ONLY BE AVAILABLE IF A USER IS ACTUALLY LOGGED IN (see header.html)

include('includes/header.html');

// If no first_name session variable exists, redirect the user:
if (isset($_SESSION['email'])) {

	// Assign session array to empty value, destroy the session and cookie, then
	// clean up the buffer, redirect the user and exit the script.

	$_SESSION = array();

	session_destroy();
	setcookie(session_name(), '', time() - 3600);
	$url = 'index.php';
	ob_end_clean();
	header("Location: $url");
	exit();
}
include('includes/footer.html');
