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

  // Get the image
  include_once("_connect.php");
  $conn = dbo();
  $image = dbo()
    ->query("SELECT * FROM images WHERE id = {$user['image_id']}")
    ->fetch(PDO::FETCH_ASSOC);
  
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

    <title>Profile</title>
  </head>

  <body>
    <?php include_once('notification.php') ?>

    <div class="container">
      <header class="jumbotron my-5">
        <div class="row">
          <div class="col-5">
            <?php if (isset($image)): ?>
              <img class="img-responsive img-thumbnail" src="<?= "uploads/{$image['name']}.{$image['ext']}" ?>" alt="<?= $image['alt'] ?>">
            <?php else: ?>
              <script src="https://www.avatarapi.com/js.aspx?email=<?= $user['email'] ?>&size=128"></script>
            <?php endif ?>
          </div>

          <div class="col-7">
            <h1 class="display-4">Hello <strong><?= "{$user['first_name']} {$user['last_name']}" ?></strong></h1>
            <p class="lead">Welcome CHAMPION!</p>
            <hr class="my-4">
            <p>
              Your personalized experience awaits <strong>YOU!</strong>
            </p>
          </div>
        </div>
      </header>

      <a class="btn" href="logout.php">Logout</a>
      <a class="btn" href="editUser.php">Edit</a>
      <a class="btn" href="deleteUser.php" onclick="return confirm('Are you sure?')">Delete Me</a>
    </div>
  </body>
</html>