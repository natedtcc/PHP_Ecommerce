
<?php

// Config file -- Set globals and functions here

// Assign admin email address
define('EMAIL', 'nnasteff@gmail.com');

// Define Location of MYSQLi config script
define ('MYSQL', 'includes/store_db_config.php');

// Define base URL for redirects
define ('BASE_URL', 'localhost');

// Set default timezone
date_default_timezone_set ('US/Eastern');


// This function displays an error to the user

function error_string_gen($error){
	
	echo '<h5 class="text-danger">' . $error . '</h5><br>';

}

// Display a success string

function success_string_gen($string){

	echo '<h5 class="text-success">' . $string . '</h5><br>';

}


// This function handles SQL queries or displays an error message if the query is
// unsuccessful

function sql_results($query, $store_db_conn){
	$result =  mysqli_query ($store_db_conn, $query) or trigger_error("Query: $query\n<br />MySQL Error: " . mysqli_error($store_db_conn));
	return $result;
}

?>