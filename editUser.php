<?php

  // If they're not logged in, redirect them
  session_start();
  if (!$_SESSION['user']) {
    $_SESSION['errors'][] = "You must log in.";
    header("Location: login.php");
    exit();
  }

  // Assign the user
  $user = $_SESSION['user'];
  $form_values = $_SESSION['form_values'] ?? $user;
  $form_values['email_confirmation'] = $form_values['email_confirmation'] ?? $user['email'];
  unset($_SESSION['form_values']);
  
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

    <title>Edit <?= "{$user['first_name']} {$user['last_name']}" ?></title>
  </head>

  <body>
    <?php include_once('notification.php') ?>
    
    <div class="container">
      <header class="jumbotron my-5">
        <h1 class="display-4">Edit <?= "{$user['first_name']} {$user['last_name']}" ?></h1>
        <p class="lead">All the fun! All the glory!</p>
        <hr class="my-4">
        <p>
          Registration will provide you access to the literally the best site on the planet. Nay, the galaxy. NAY! <strong>THE UNIVERSE!</strong>
        </p>
      </header>

      <section class="mb-5">
        <form action="./update.php" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="first_name">First Name:</label>
                <input class="form-control" type="text" name="first_name" required placeholder="Herman" value="<?= $form_values['first_name'] ?? null ?>">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input class="form-control" type="text" name="last_name" required placeholder="Munster" value="<?= $form_values['last_name'] ?? null ?>">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="email">Email:</label>
                <input class="form-control" type="email" name="email" placeholder="herman.munster@mockingbird.com" required value="<?= $form_values['email'] ?? null ?>">
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label for="email_confirmation">Email Confirmation:</label>
                <input class="form-control" type="email" name="email_confirmation" placeholder="herman.munster@mockingbird.com" required value="<?= $form_values['email_confirmation'] ?? null ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="form-group">
              <label for="avatar">Upload your avatar:</label>
              <input type="file" name="avatar">
            </div>
          </div>

          <button class="btn" type="submit">Update</button>
          <a class="btn" href="login.php">Login</a>
        </form>
      </section>
    </div>

    <!-- Add the recaptcha scripts -->
  </body>
</html>