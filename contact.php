<?php # contact.php - N. Nasteff

// Users can use this page to contact the store admin.

// Set page title, include header file

$page_title = 'Contact Us';
include('includes/header.html');

// Page submission - if a message has been sent...

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Trim all incomming post data..

	$trimmed = array_map('trim', $_POST);

	// Assume false values for the user's input..

	$name = $email = $message = FALSE;

	// Validate inputs..

	if (isset($trimmed['name'])){
		
		$name = htmlspecialchars($trimmed['name']);
	}

	if (isset($trimmed['email']) && filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL)){

		$email = $trimmed['email'];
	}

	else {
		error_string_gen('Invalid email. Try again.');
	}

	if (isset($trimmed['message'])){

		$message = htmlspecialchars($trimmed['message']);
	}


	// If all forms are validated...

	if ($name && $email && $message){

		// Build string to be sent via email
		
		$email_string = "You have received a message from a potential customer on your website<br><br>Name: $name<br>
						Email: $email<br>Message:<br><br>$message<br>";

		// Send the message

		send_email(EMAIL, $email_string, 'Message Received');
		
		echo '<center><h3>Thank you, your message has been sent.<br>Please allow 1-2 business days for a reply.</h3>';

	}

	// If form validation fails..

	else {

		error_string_gen('You entered some bad information. Try again!');
	}

}

// If request method is not post, display the form to send a message to the store admin

elseif ($_SERVER['REQUEST_METHOD'] != 'POST') {

echo '<h3 class="display-3"><center>Contact Us</h3>
<table class="center"><tr><td><p>Need to contact us directly? Fill out the forms below and click submit. We will get back to you as soon as possible. Thank you!</td></tr></table>
	<form action="contact.php" method="post">
		<center><fieldset>
			<p><b>Name:<br></b> <input type="text" required="required" name="name" size="30" maxlength="30"/></p>
			<p><b>Email Address:<br></b> <input type="email" required="required" name="email" size="30" maxlength="60"/> </p>
			<p><b>Enter your message below:</b><br> <textarea required="required" name="message" rows="7" cols="40"></textarea> <br></p>
			<input type="submit" value="Submit" /></p>
		</fieldset>
	</form>';
}

// Any other situation...

else{

	error_string_gen('Ooops, looks like you messed up. Hit back and try again.<br>Make sure you enter all your information correctly!');


}

include('includes/footer.html'); ?>

