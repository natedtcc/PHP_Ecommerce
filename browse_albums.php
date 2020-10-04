<?php # browse_albums.php - N. Nasteff

// This page displays all available albums from the store database, and paginates them

include('includes/header.html');
require(MYSQL);

// Define number of albums to display per page..

$display = 10;

// If page numbers have already been assigned..

if (isset($_GET['p']) && is_numeric($_GET['p'])) {
	$pages = $_GET['p'];
}

// Else, determine number of pages

else {

	// Count number of records in the product DB..
	$count_query = "SELECT COUNT(product_id) FROM products";
	$result = @mysqli_query($store_db_conn, $count_query);
	$row = @mysqli_fetch_array($result, MYSQLI_NUM);
	$product_count = $row[0];

	// Calculate the number of pages...
	if ($product_count > $display) {
			$pages = ceil($product_count / $display);
	} else {
			$pages = 1;
	}
}

// Determine where in the database to start returning results...
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
	$start = $_GET['s'];
} 

else {
	$start = 0;
}

echo '<h3 class="display-3">Browse Albums<br></h3>';

// Define the query:
$product_query = "SELECT * FROM products ORDER BY artist ASC LIMIT $start, $display";
$result = mysqli_query($store_db_conn, $product_query); // Run the query.

// Table header:
echo '<table cellpadding="10" align="center">';


// Fetch and print all the records....
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	echo '<tr><td><img src="img/' . $row['image'] 
		. '.jpg" style="width:120px;height:120px;"></td><td><b>' 
		. $row['title'] . '</b><br>' . $row['artist'] . '<br>$' 
		. $row['price'] . '<br><a href="add_cart.php?aid='
		. $row['product_id'] . '"">Add to Cart</a></td>';
} // End of WHILE loop.

echo '</table>';
mysqli_free_result($result);
mysqli_close($store_db_conn);

// Make the links to other pages, if necessary.
if ($pages > 1) {

	echo '<br /><p>';
	$current_page = ($start / $display) + 1;

	// If it's not the first page, make a Previous button:
	if ($current_page != 1) {
			echo '<a href="browse_albums.php?s=' . ($start - $display) . '&p=' 
					. $pages . '"><<</a> ';
	}

	// Make all the numbered pages:
	for ($i = 1; $i <= $pages; $i++) {
		if ($i != $current_page) {
				echo '<a href="browse_albums.php?s=' . (($display * ($i - 1))) .
						'&p=' . $pages . '">' . $i . '</a> ';
		} 

		else echo $i . ' ';
	}

	// If it's not the last page, make a Next button:
	if ($current_page != $pages) {
		echo '<a href="browse_albums.php?s=' . ($start + $display) 
			. '&p=' . $pages . '">>></a>';
	}

	// Close <p> tag...

	echo '</p>';
}

include('includes/footer.html');
