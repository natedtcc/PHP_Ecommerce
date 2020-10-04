<?php # add_cart.php - N. Nasteff

// This page will add the selected album to the shopping cart

// Set the page title and include the HTML header:

$page_title = 'Add to Cart';
include ('includes/header.html');

 // Check for an album ID.

if (isset ($_GET['aid']) 
	&& filter_var($_GET['aid'], FILTER_VALIDATE_INT, array('min_range' => 1))){
	
	$aid = (int)$_GET['aid'];

	// Check if the cart already contains one of these prints;
	// If so, increment the quantity:
	if (isset($_SESSION['cart'][$aid])) {

		$_SESSION['cart'][$aid]['quantity']++; // Add another.

		// Display a message:
		echo '<h3 class="display-3"><center>Added to cart!</h3>';
		
	} 

	// Add product to cart

	else {

		// Retrieve album price from database..
		
		require(MYSQL);
		$price_query = "SELECT price FROM products WHERE product_id=$aid";
		$result = mysqli_query ($store_db_conn, $price_query);

		// If product ID is valid...

		if (mysqli_num_rows($result) == 1) {
	
			list($price) = mysqli_fetch_array ($result, MYSQLI_NUM);
			
			// Add to the session cart

			$_SESSION['cart'][$aid] = array ('quantity' => 1, 'price' => $price, 'prod_id' => $aid);

			// Display confirmation

			echo '<h3 class="display-3"><center>Added to cart!</h3>';

		}

		// If an invalid product ID is received...

		else { 
			error_string_gen('This page has been accessed in error!');		}
		
		mysqli_close($store_db_conn);
		exit();

	} 

} 

// If no product ID was received...

else {
	error_string_gen('This page has been accessed in error!');
}

include ('includes/footer.html');
?>
