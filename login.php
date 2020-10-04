<?php # login.php - N. Nasteff

// This page creates the login form which is then posted to itself
// and processed accordingly. 

$page_title = 'Login';
include ('includes/header.html');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	
	require('includes/store_db_config.php');

	// Assume false values for validation..

	$email = $password = FALSE;
	
	// Validate the email address:
	if (!empty($_POST['email'])) {
		$email = mysqli_real_escape_string ($store_db_conn, $_POST['email']);
	} 

	else {
		error_string_gen('You forgot to enter your email address!</h5>');
	}
	
	// Validate the password:
	if (!empty($_POST['password'])) {
		$password = 
			mysqli_real_escape_string ($store_db_conn, $_POST['password']);
	} 

	else {
		error_string_gen('You forgot to enter your password!</h5>');
	}
	
	// If email and pass are validated..

	if ($email && $password) {

		// Query the database:
		$customer_query = "SELECT email, password, customer_id FROM customers "
			. "WHERE (email='$email' AND password=SHA1('$password'))";		
		$result = mysqli_query ($store_db_conn, $customer_query) or 
			trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		
		// If the user is found in the DB...	

		if (@mysqli_num_rows($result) == 1) { 

			// Register the login values (keeps the contents of the
			// cart in the session (if the user was not logged in while 
			// adding items to the cart)

			$_SESSION = $_SESSION + mysqli_fetch_array ($result, MYSQLI_ASSOC); 
			mysqli_free_result($result);
			mysqli_close($store_db_conn);
							
			// Redirect the user, clear buffer and quit the script
			
			$url = 'index.php';
			ob_end_clean();
			header("Location: $url");
			exit();
				
		} 

		// Username/password mismatch or other invalid login attempt...

		else {
			error_string_gen('Invalid credentials. Try again.');
		}
		
	} 

	else {
		error_string_gen('Please try again.');
	}
	
	mysqli_close($store_db_conn);

} 
?>

<h3 class="display-3 text-center">
	Login
</h3>
<p>
	<small>
		Your browser must allow cookies in order to log in.
	</small>
</p>
<form action="login.php" method="post">
	<fieldset>
		<p>
			Email Address:<br>
			<input type="email" required="required" name="email" 
				size="20" maxlength="60" />
		</p>
		<p>
			Password:<br>
			<input type="password" required="required" name="password" 
				size="20" maxlength="20" />
		</p>
		<input type="submit" name="submit" value="Login" />
	</fieldset>
</form>
<br>
<p>
	Don't have an account? 
	<a href="register.php">
		Click here to register.
	</a>
</p>

<?php include ('includes/footer.html'); ?>
