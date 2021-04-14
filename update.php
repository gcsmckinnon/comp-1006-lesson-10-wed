<?php

  /* VALIDATION */
  // Build an error handling function
  session_start();
  function error_handler ($errors) {
    if (count($errors) > 0) {
      $_SESSION['errors'] = $errors;
      $_SESSION['form_values'] = $_POST;

      header("Location: profile.php");
      exit;
    }
  }

  // Create an array to hold all the field errors
  $errors = [];

  // Collect our fields
  $first_name = filter_input(INPUT_POST, 'first_name');
  $last_name = filter_input(INPUT_POST, 'last_name');
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $email_confirmation = filter_input(INPUT_POST, 'email_confirmation');

  // Validate the necessary fields are not empty
  $required_fields = [
    'first_name',
    'last_name',
    'email',
    'email_confirmation'
  ];

  foreach ($required_fields as $field) {
    if (empty($$field)) {
      $human_field = str_replace("_", " ", $field);
      $errors[] = "You cannot leave the {$human_field} blank.";
    } else {
      $$field = filter_var($$field, FILTER_SANITIZE_STRING);
    }
  }

  // Validate the email is in the correct format
  if (!$email) {
    $errors[] = "The email isn't in a valid format.";
  }

  // Validate the email matches the email_confirmation
  if ($email !== $email_confirmation) {
    $errors[] = "The email doesn't match the email confirmation field.";
  }
  
  // Check if there errors
  error_handler($errors);

  /* END OF VALIDATION */

  /* NORMALIZATION */
  // Normalize the string fields (convert to lowercase and capitalize the first letter)

  // Lowercase the email
  $email = strtolower($email);
  /* END NORMALIZATION */

  /* SANITIZATION */
  // Sanitize all values on their insertion
  require_once('_connect.php');
  $conn = dbo();

  // Uploading image
  if (count($_FILES) > 0) {
    $target = "./uploads/{$_FILES['avatar']['name']}";
    move_uploaded_file($_FILES["avatar"]["tmp_name"], $target);

    $path_info = pathinfo($_FILES['avatar']['name']);
    $extension = filter_var($path_info['extension'], FILTER_SANITIZE_STRING);
    $filename = filter_var($path_info['filename'], FILTER_SANITIZE_STRING);
    $type = filter_var($_FILES['avatar']['type'], FILTER_SANITIZE_STRING);
    
    $sql = "INSERT INTO images (
      name,
      alt,
      ext,
      mime
    ) VALUES (
      :name,
      :alt,
      :ext,
      :mime
    )";

    $alt = "This is an image";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $filename, PDO::PARAM_STR);
    $stmt->bindParam(':alt', $alt, PDO::PARAM_STR);
    $stmt->bindParam(':ext', $extension, PDO::PARAM_STR);
    $stmt->bindParam(':mime', $type, PDO::PARAM_STR);
    $stmt->execute();
    $image_id = $conn->lastInsertId();
  }

  $sql = "UPDATE users SET 
    first_name = :first_name,
    last_name = :last_name,
    email = :email,
    image_id = :image_id
    WHERE id = {$_SESSION['user']['id']}
  ";
  $stmt = $conn->prepare($sql);

  // Sanitize using the binding
  $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR); // Casts it to a string
  $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR); // Casts it to a string
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  if (isset($image_id)) $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
  
  /* END SANITIZATION */

  // fetch our new user details
  $user = $conn
    ->query("SELECT * FROM users WHERE id = {$_SESSION['user']['id']}")
    ->fetch(PDO::FETCH_ASSOC);
  $_SESSION['user'] = $user;

  // Insert our row
  try {
    $stmt->execute();
    $_SESSION['successes'][] = "You updated your profile successfully.";
    header("Location: profile.php");
    exit;
  } catch (Exception $error) {
    $errors[] = $error->getMessage();
    error_handler($errors);
  }

  // Check for SQL errors

  // If there are any errors, respond with them
  