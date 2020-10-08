<?php # error_handler.php - N. Nasteff

// This is the custom error handler script which handles all errors while
// the site is either live or in development mode.

// Variable for if the site is live or in developement
define('LIVE', TRUE);


function error_handler ($e_number, $e_message, $e_file, $e_line, $e_vars) {

	// Build error log message
	$message = "An error occurred in script '$e_file' on line $e_line: $e_message\n";
	
	// Append $e_vars to  $message:
	$message .= print_r ($e_vars, 1);

    // If in dev mode, print comprehensive errors
	if (!LIVE) {
		echo '<pre>' . $message . "\n";
		debug_print_backtrace();
		echo '</pre><br />';
    } 
    
    // If live, display an error message.
    else {
        error_log($e_message);
		echo '<center><h4 class="display-4">A system error occurred. '
		 .'We apologize for the inconvenience.</h4><br />';		
	}

}

// Use custom error handler:
set_error_handler ('error_handler');


?>