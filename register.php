<?php # register.php - N. Nasteff

// This page is used to register users on the website.

$page_title = 'Register';
include ('includes/header.html');

// Handle the registration form via POST...

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


  require(MYSQL);
  
  // Trim all the incoming data:
  $trimmed = array_map('trim', $_POST);

  // Assume invalid values:
  $first_name = $last_name = $email = $password = FALSE;
  
  // Check for a first name:
  if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $trimmed['first_name'])) {
    $first_name = 
      mysqli_real_escape_string ($store_db_conn, $trimmed['first_name']);
  } 
  
  else error_string_gen('Please enter your first name!');
  
  // Check for a last name:
  if (preg_match ('/^[A-Z \'.-]{2,40}$/i', $trimmed['last_name'])) {
    $last_name = 
      mysqli_real_escape_string ($store_db_conn, $trimmed['last_name']);
  } 
  
  else error_string_gen('Please enter your last name!');
  
  // Check for an email address:
  if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL)) {
    $email = 
      mysqli_real_escape_string ($store_db_conn, $trimmed['email']);
  }

  else error_string_gen('Please enter a valid email address!');
  

  // Check for a password and match against the confirmed password:
  if (preg_match ('/^\w{4,20}$/', $trimmed['password1']) ) {
    if ($trimmed['password1'] == $trimmed['password2']) {
      $password = mysqli_real_escape_string ($store_db_conn, 
        $trimmed['password1']);
    } 
    
    else error_string_gen('Your password did not match the confirmed password!');
  } 
  
  else error_string_gen('Please enter a valid password!');

  // If all user entered data is valid...
  
  if ($first_name && $last_name && $email && $password) {

    // Check is email is in use already...

    $customer_query = "SELECT customer_id FROM customers WHERE email='$email'";
    $result = mysqli_query ($store_db_conn, $customer_query) or 
      trigger_error("Query: $customer_query\n<br />MySQL Error: " . 
        mysqli_error($store_db_conn));
    
    // If the user's email has not been registered (success)...

    if (mysqli_num_rows($result) == 0) {

      // Add the user to the database...

      $register_query = "INSERT INTO customers "
        . "(first_name, last_name, email, password, reg_date) "
        . "VALUES ('$first_name', '$last_name', '$email',  "
        . "SHA1('$password'), NOW() )";
    
      $result = mysqli_query ($store_db_conn, $register_query) 
        or trigger_error("Query: $register_query\n<br />MySQL Error: " 
          . mysqli_error($store_db_conn));

      // If the query was successful...

      if (mysqli_affected_rows($store_db_conn) == 1) {
        
        // Display confirmation..

        success_string_gen('Thank you for registering!<br>You may login by '
          . 'clicking <a href="login.php">here</a>.');

         
        // Quit the script
        
        exit();

      }

      // If the query did was unsuccessful...

      else {
        error_string_gen('You could not be registered due to a system error. '
          . 'We apologize for any inconvenience.');
      }
      
    } 

    // If email is already in use...

    else {
      error_string_gen('That email address has already been registered.');
    }
    
  } 

  // If validation fails...

  else {
    error_string_gen('Please try again.');
  }

  mysqli_close($store_db_conn);

}

?>

<!-- BUILD USER REGISTRATION FORM -->
  
<h3 class="display-3 text-center">
  Register
</h3>
<br>
<form action="register.php" method="post">
  <fieldset>
    <p>
      First Name:<br> 
      <input type="text" required="required" name="first_name"
      size="20" maxlength="20" value="
        <?php if (isset($trimmed['first_name'])) 
        echo $trimmed['first_name'];
        ?>" 
      />
    </p>
  
    <p>
      Last Name:<br> 
      <input type="text" required="required" name="last_name" 
      size="20" maxlength="40" value="
        <?php if (isset($trimmed['last_name'])) 
        echo $trimmed['last_name'];
        ?>"
      />
    </p>

    <p>
      Email Address:<br>
      <input type="text" required="required" name="email"
      size="30" maxlength="60" value="
        <?php if (isset($trimmed['email'])) 
          echo $trimmed['email']; 
        ?>" 
      />
    </p>
    
    <p>
      Password:<br>
      <input type="password" required="required" name="password1"
      size="20" maxlength="20" value="
        <?php if (isset($trimmed['password1'])) 
        echo $trimmed['password1'];
        ?>" 
      />
      <br>
      <small>Use only letters, numbers, and the underscore. 
        Must be between 4 and 20 characters long.
      </small>
    </p>

    <p>
      Confirm Password:<br> 
      <input type="password" required="required" name="password2" 
      size="20" maxlength="20" value="
        <?php if (isset($trimmed['password2'])) 
        echo $trimmed['password2'];
        ?>" 
      />
    </p>
  </fieldset>
  
  <input type="submit" name="submit" value="Register" /></div>

</form>

<?php include ('includes/footer.html'); ?>
