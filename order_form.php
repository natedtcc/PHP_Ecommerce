<?php # order_form.php - N. Nasteff

// This page gathers the user's address and shipping information
// Once all info is gathered, the user submits their order and receives
// a receipt via email (and on-screen). The contents of the order
// are inserted into the ecommerce database

$page_title = 'Place an order';

// If an order has been sent, set page title to a confirmation sting

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $page_title = 'Thank you for your order';
}

// Include header and mailscript.php (for email generation)

include('includes/header.html');
include('includes/mailscript.php');
require(MYSQL);



if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($_SESSION['cart']) > 0) {

  // Trim and validate all post data,
  $trimmed = array_map('trim', $_POST);

  // Assume false values..

  $name = $address = $address2 = $city = $state = $zip =
    $order_id = $shipping_id = $phone = FALSE;

  // Assign shipping_ids to an array

  $shipping_array = array(1, 2, 3);

  // Assign an address_id for the database

  $address_id = rand(10000, 999999);

  // Validate all user data....

  if (isset($trimmed['order_id']) && is_numeric($trimmed['order_id'])) {
  $order_id = $trimmed['order_id'];
  }

  if (isset($trimmed['shipping']) && in_array($trimmed['shipping'], $shipping_array)) {
  $shipping_id = $trimmed['shipping'];
  }

  if (preg_match('/^[A-Z \'.-]{2,20}$/i', $trimmed['name'])) {
    $name = mysqli_real_escape_string($store_db_conn, $trimmed['name']);
  } else {
    error_string_gen('Please enter your first name!');
  }

  if (isset($trimmed['address'])) {
    $temp_address = mysqli_real_escape_string($store_db_conn, $trimmed['address']);
    $address = htmlspecialchars($temp_address);
  } else {
    error_string_gen('Please enter your address!');
  }

  if (isset($trimmed['address2'])) {
    $temp_address = mysqli_real_escape_string($store_db_conn, $trimmed['address2']);
    $address2 = htmlspecialchars($temp_address);
  } else {
    $address2 = " ";
  }

  if (isset($trimmed['city'])) {
    $temp_city = mysqli_real_escape_string($store_db_conn, $trimmed['city']);
    $city = htmlspecialchars($temp_city);
  } else {
    error_string_gen('Invalid city name!');
  }

  if (isset($trimmed['state']) && strlen($trimmed['state']) == 2) {
    $state = htmlspecialchars($trimmed['state']);
  } else {
    error_string_gen('Enter a proper state!');
  }

  if (isset($trimmed['zip']) && is_numeric($trimmed['zip'])) {
    $zip = $trimmed['zip'];
  } else {
    error_string_gen('Enter a proper zip!');
  }

  if (isset($trimmed['phone']) && preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $trimmed['phone'])){
    $phone = $trimmed['phone'];

  }

  if (isset($trimmed['receipt_sub'])) {
    $subtotal = $trimmed['receipt_sub'];
  }

  // Get shipping cost from the database..

  $cost_query = "SELECT cost FROM shipping WHERE shipping_id=$shipping_id";
  $result = sql_results($cost_query, $store_db_conn);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $shipping_cost = (float) $row['cost'];

  $total = $subtotal + $shipping_cost;

  // Assign cart product ids to an array..

  $aid_array = array();
  $i = 0;
  foreach ($_SESSION['cart'] as $x => $y) {
    $aid_array[$i] = (int) $x;
    $i++;
  }

  // If validation has passed..

  if ($name && $address &&  $city && $state && $zip && $order_id && $shipping_id && $phone) {

    // Assign session email to a variable..

    $email = $_SESSION['email'];
    $customer_id = $_SESSION['customer_id'];

    // Build query for orders table..

    mysqli_begin_transaction($store_db_conn, MYSQLI_TRANS_START_READ_WRITE);

    $insert_order = "INSERT INTO orders (`order_id`, `customer_id`, `order_date`, `total`, `shipping_id`, `address_id`) VALUES
    ($order_id, " . $_SESSION['customer_id'] . ", NOW(), $total, $shipping_id, $address_id)";

    $address_insert = "INSERT INTO shipping_address (`address_id`, `order_id`, `ship_name`, `address`, `address2`, `city`, `state`, `zip`, `phone`) VALUES ($address_id, $order_id, '$name', '$address', '$address2', '$city', '$state', $zip, '$phone')";

    $customer_update = "UPDATE customers SET address_id=$address_id WHERE customer_id=$customer_id";

    $result_a = sql_results($insert_order, $store_db_conn);
    $result_b = sql_results($address_insert, $store_db_conn);
    $result_c = sql_results($customer_update, $store_db_conn);

    if ($result_a && $result_b && $result_c){

      mysqli_commit($store_db_conn);

    // Create insert statements for the order_contents database

    foreach ($aid_array as $product_id) {
      $sql = "INSERT INTO order_contents (`order_id`, `product_id`, `quantity`) VALUES ($order_id, $product_id, "
        . $_SESSION['cart'][$product_id]['quantity'] . ")";

      $result = mysqli_query($store_db_conn, $sql);
    }
          // Retrieve order date

    $order_date = date("m-d-Y",time());

    success_string_gen('Thank you! Your order has been placed.<br>Your order number is
    ' . $order_id . '.<br>Order date:<br>'.$order_date);

    echo '<h5><center>You will receive an email with a copy of your recipt.</h5>';

    $cart_query = "SELECT product_id, title, artist, price, image FROM products WHERE product_id IN (";
    foreach ($_SESSION['cart'] as $prod_id => $value) {
      $cart_query .= $prod_id . ',';
    }
    $cart_query = substr($cart_query, 0, -1) . ') ORDER BY price ASC';
    $result = mysqli_query($store_db_conn, $cart_query);


    echo '<br><br>';

    // Create a table to display the results (One for the user's email receipt,
    // one for the actual page output.)

    $email_string = '<h3 class="display"><center>Thank you for your order!</h3><center><br><br><p>Your order number is: <br>' . $order_id . '
    <br>Order date:<br>'. $order_date .'<br><br>Contents:</p><table border="0" width="50%" cellspacing="0" cellpadding="3" align="center">
    <th align="left"><b>Album</b></th>
    <th align="left"><b>Artist</b></th>
  <th align="left"><b>Price</b></th>
  <th align="left"><b>Qty</b></th>';

    echo '<table border="0" width="50%" cellspacing="0" cellpadding="3" align="center">
		<th><b>Album</b></th>
		<th><b>Artist</b></th>
		<th><b>Price</b></th>
		<th><b>Qty</b></th>
	
	';

    // Display each item in the cart..

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

      // Append email string with cart values
      $email_string .= "\t<tr>
		<td>{$row['title']}</td>
		<td>{$row['artist']}</td>
		<td>\${$_SESSION['cart'][$row['product_id']]['price']}</td>
		<td>{$_SESSION['cart'][$row['product_id']]['quantity']}</td>
    </tr>\n";

      // Print out cart contents..
      echo "\t<tr class=\"lead\">
		<td>{$row['title']}</td>
		<td>{$row['artist']}</td>
		<td>\${$_SESSION['cart'][$row['product_id']]['price']}</td>
		<td>{$_SESSION['cart'][$row['product_id']]['quantity']}</td>
    </tr>\n";
    }

    // Print out subtotal, shipping cost, total and shipping address..

    echo "</table><br><p class=\"lead\"><b>Subtotal:</b> <br>\$$subtotal<br><br><b>Shipping cost:</b><br>\$$shipping_cost<br><br><b>Total:</b><br>\$$total<br><br><b>Shipping address:</b><br>$name<br>
  $address<br>$city, $state $zip<br>$phone<br>";

    // Append email string with subtotal, shipping cost, total and shipping address...

    $email_string .= "</table><p><b>Subtotal:</b> <br>\$$subtotal<br><br><b>Shipping cost:</b><br>\$$shipping_cost<br><br><b>Total:</b><br>\$$total<br><br><b>Shipping address:</b><br>$name<br>
  $address<br>$city, $state $zip<br>$phone<br><br><h2>Thanks for shopping at the Jazz Lounge!</h2>";

    // Prepare and send the mail..

    $mail = send_email($email, $email_string, 'Order confirmation');
    $mail->send();

    // Clear the shopping cart and exit the script..

    $_SESSION['cart'] = array();

    exit();
  }
}

}

// If the user is logged in and has contents in their cart, generate the order page..

if (isset($_SESSION['email']) && !isset($_POST['order_id']) && count($_SESSION['cart']) > 0) {



  // Assign cart subtotal to variables from session

  $subtotal = $total = $_SESSION['subtotal'];

  echo "<center><h3 class=\"display-3\">Place your order</h3> <p class=\"lead\"><br>Your subtotal: </p><p id=\"subtotal\" class=\"blockquote\">\$$subtotal</p>
      <form action=\"order_form.php\" method=\"post\">
        <fieldset>
        <p class=\"lead\"><select name=\"shipping\" id=\"shipping\" onchange=\"calc_total()\" style=\"width: 275px;\" required=\"required\">
            <option value=\"\">Select a shipping option..</option>";


  $shipping_query = "SELECT * from shipping";
  $result = sql_results($shipping_query, $store_db_conn);

  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
    echo '<option value="' . $row['shipping_id'] . '">' . $row['carrier'] . ' ($' . $row['cost'] . ')</option>';
  }
  echo "</select></p><br><p class=\"lead\">
    Your total is:  </p><p id=\"total\" class=\"lead\"><b>\$$subtotal</b></p>";

  // Generate random number for order_id..

  $order_id = rand(10000000, 999999999);

  // Create inputs for the user to enter their shipping info

  echo '<br><p>Enter your information and select your shipping options below:</p>
 <p>Name:<br>
 <input type="text" name="name" required="required" length="30"/><br>
<p>Address:<br>
  <input type="text" name="address" required="required" length="30"/><br>
 <p> Address line 2<br>
  <input type="text" name="address2" /><br></p>
  <p>City<br>
  <input type="text" name="city" required="required" /><br></p>
  <p>State  
  <select name="state" style="width: 50px;" required="required" ></p>
  	<option value="">--</option>';
  // Populate the state dropdown with data from the database..

  $state_query = "SELECT code from states";
  $result = sql_results($state_query, $store_db_conn);
  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo '<option value="' . $row['code'] . '">' . $row['code'] . '</option>';
  }
  // Close the dropdown

  echo '</select><br>';

  // Zip code and Telephone forms, and some hidden forms for printing the recipt
  echo '<p>Zip Code<br>
         <input type="text" name="zip" required="required" /><br></p>
         <p>Telephone<br>
         <input type="tel" name="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required="required" /><br><small>format: 123-456-7890</small></p>
          <input name="order_id" type="hidden" value="' . $order_id . '" />
          <input name="receipt_sub" type="hidden" value="' . $subtotal . '" />
          <input name="receipt_total" type="hidden" value="' . $total . '" />
          <input type="submit" value="Place your order" />
          </fieldset>
          </form>
          </select>
          </center>';
}

if (count($_SESSION['cart']) == 0) {
  echo "<h3 class=\"display\">Your cart is currently empty.</h3>";
}

if (!isset($_SESSION['email'])){

  error_string_gen('You must be logged in to view this page!');
}


include('includes/footer.html');
