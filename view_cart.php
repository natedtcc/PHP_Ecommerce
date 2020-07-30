<?php # view_cart.php - N. Nasteff

// This page will display the contents of the user's shopping 
// cart, or update the quantities of items already added

$page_title = 'View Your Shopping Cart';
include ('includes/header.html');

// Check for form submission, in case user updates quantity of items previously added

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Change the quantities..

	foreach ($_POST['qty'] as $k => $v) {

		// Force integer values
		
		$prod_id = (int) $k;
		$qty = (int) $v;
		
		// If an item is set to 0 quantity (remove from cart)...

		if ( $qty == 0 ) { 
			unset ($_SESSION['cart'][$prod_id]);
		} 

		// If quantity is altered (increased or decreased to a value greater than 0)..

		elseif ( $qty > 0 ) {
			$_SESSION['cart'][$prod_id]['quantity'] = $qty;
		}
		
	}
	
}

// Retrieve cart (if it isn't empty)...

if (!empty($_SESSION['cart'])) {

	// Gather information about albums in the cart from the database...

	require(MYSQL);;
	
	// Create an SQL query based on each product_id found in the $_SESSION['cart'] array

	$cart_query = "SELECT product_id, title, artist, price, image FROM products WHERE product_id IN (";
	foreach ($_SESSION['cart'] as $prod_id => $value) {
		$cart_query .= $prod_id . ',';
	}
	$cart_query = substr($cart_query, 0, -1) . ') ORDER BY price ASC';
	$result = mysqli_query ($store_db_conn, $cart_query);
	
	// Create a table to display the results, as well as a form for updating quantities

	echo '<form action="view_cart.php" method="post">
	<table class="center">
	
		<th align="left" ></th>
		<th align="left" ><b>Album</b></th>
		<th align="left" ><b>Artist</b></th>
		<th align="right"><b>Price</b></th>
		<th align="center"><b>Qty</b></th>
		<th align="right"><b>Price</b></th>

	';

	// Display each item in the cart..

	// Create variable for total cost..

	$total = 0; 
	while ($row = mysqli_fetch_array ($result, MYSQLI_ASSOC)) {
	
		// Calculate the total and sub-totals.
		$subtotal = $_SESSION['cart'][$row['product_id']]['quantity'] * $_SESSION['cart'][$row['product_id']]['price'];
		$total += $subtotal;
		
		// Display results (including image of the album) as tabledata
		// Add text+

		echo "\t<tr>
		<td align=\"left\"><img src=\"img/".$row['image'].".jpg\" style=\"width:60px;height:60px;\">
		<td align=\"left\">{$row['title']}</td>
		<td align=\"left\">{$row['artist']}</td>
		<td align=\"right\">\${$_SESSION['cart'][$row['product_id']]['price']}</td>
		<td align=\"center\"><input type=\"number\" min=\"0\" size=\"3\" style=\"width: 30px;\" name=\"qty[{$row['product_id']}]\" value=\"{$_SESSION['cart'][$row['product_id']]['quantity']}\" /></td>
		<td align=\"right\">$" . number_format ($subtotal, 2) . "</td>
		</tr>\n";


	
	}

	// Close the database connection...

	mysqli_close($store_db_conn);

	// Display the total price, close table/form tags, and create
	// a submit button to update the cart...

	echo '<tr>
		<td colspan="4" align="right"><b>Total:</b></td>
		<td align="right">$' . number_format ($total, 2) . '</td>
	</tr>
	</table>
	<div align="center"><p><center><input type="submit" name="submit" value="Update My Cart" />
	</form><center>Enter a quantity of 0 to remove an item.
	<br /> <br><h5><a href="order_form.php">Checkout</a></p></h5></div>';

	$_SESSION['subtotal'] = number_format ($total, 2);

} 

// If cart is currently empty...

else {
	echo '<h3 class="display-4">Your cart is currently empty.</h3>';
}

include ('includes/footer.html');
?>