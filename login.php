<?php
//login.php

include 'db_config.php';

if (isset($_SESSION['user_id'])) {
    header('location:index.php');
}

$message = '';

if (isset($_POST['login'])) {
    $query = '
 SELECT * FROM user 
  WHERE email = :email
 ';
    $statement = $connect->prepare($query);
    $statement->execute(
  array(
    'email' => $_POST['email'],
  )
 );
    $count = $statement->rowCount();
    if ($count > 0) {
        $result = $statement->fetchAll();
        foreach ($result as $row) {
            if ($row['email_status'] == 'verified') {
                if (password_verify($_POST['password'], $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    header('location:index.php');
                } else {
                    $message = '<label>Wrong Password</label>';
                }
            } else {
                $message = "<label class='text-danger'>Please First Verify, your email address</label>";
            }
        }
    } else {
        $message = "<label class='text-danger'>Wrong Email Address</label>";
    }
}

?>

<!DOCTYPE html>
<html>
 <head>
  <title>OIMS Login</title>  
  <script src="assets/js/jquery.min.js"></script>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <script src="assets/js/bootstrap.min.js"></script>
 </head>
 <body>
  <br />
  <div class="container" style="width:100%; max-width:600px">
   <h2 align="center">OIMS::Login</h2>
   <br />
   <div class="panel panel-default">
    <div class="panel-heading"><h4>Login</h4></div>
    <div class="panel-body">
     <form method="post">
      <?php echo $message; ?>
      <div class="form-group">
       <label>User Email</label>
       <input type="email" name="user_email" class="form-control" required />
      </div>
      <div class="form-group">
       <label>Password</label>
       <input type="password" name="user_password" class="form-control" required />
      </div>
      <div class="form-group">
       <input type="submit" name="login" value="Login" class="btn btn-info" />
      </div>
     </form>
     <p align="right">New user to OIMS?Please <a href="signup.php">Register here</a></p>
    </div>
   </div>
  </div>
 </body>
</html>