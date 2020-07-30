<?php #store_db_config.php - N. Nasteff

// Define the database connection values here, and establish a connection

DEFINE ('DB_USER', 'natedtcc');
DEFINE ('DB_PASSWORD', '*******');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'my_ecommerce');

$store_db_conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
// Check connection
if ($store_db_conn->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>