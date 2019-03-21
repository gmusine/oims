<?php

include 'db_config.php';

if (isset($_SESSION['user_id'])) {
    header('location:index.php');
}
$message = '';
if (isset($_POST['signup'])) {
    $query = 'SELECT * FROM user WHERE user_email = :user_email';
    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':email' => $_POST['email'],
        )
        );
    $no_of_row = $statement->rowCount();
    if ($no_of_row > 0) {
        $message = '<label class="text-danger">Email Already Exists</label>';
    } else {
        $password = rand(100000, 999999);
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
        $activation_code = md5(rand());
        $insert_query = '
            INSERT INTO user (user_name, email, password, user_activation_code, email_status) VALUES(:user_name, :email, :password, :activation_code, :email_status)';
        $statement = $connect->prepare($insert_query);
        $statement->execute(
                array(
                    ':user_name' => $_POST['user_name'],
                    ':email' => $_POST['email'],
                    ':password' => $encrypted_password,
                    ':activation_code' => $activation_code,
                    'email_status' => 'not verified',
                )
                );
        $result = $statement->fetchAll();
        if (isset($result)) {
            $base_url = 'http://localhost/oims/email';
            $mail_body = '
                <p>Hello '.$_POST['user_name'].',</p>
                <p>Thanks for registering with OIMS.</p>
                <p>Please open this link to activate your account by verifying your email.".$base_url."email_verification.php?activation_code=".$activation_code."</p>
                <p>Best regards.,<br/>OIMS</p>
            ';
            require 'class/class.phpmailer.php';
            $mail = new phpmailer();
            $mail->IsSMTP();            //sets Mailer to send message using SMTP
            $mail->Host = 'smtpout.secureserver.net';   //Sets the SMTP hosts of your Email hosting.
            $mail->Port = '80';         //Sets the SMTP default server port
            $mail->SMTPAuth = 'true';       //Sets SMTP authentication. utilizes the username and password variables.
            $mai->Username = 'xxxxxxxxx';   //sets SMTP username
            $mail->Password = 'xxxxxxxxx';      //sets SMTP password
            $mail->SMTPSecure = '';     //Sets connection prefix. Options are "", "ssl" or "tls"
            $mail->From = 'musinegeorge@gmail.com';     //Sets the from email address for the message
            $mail->FromName = 'George Musine';      //sets the From name of the message
            $mail->AddAddress($_POST['email'], $_POST['user_name']);    //adds a "To" address.
            $mail->WordWarap = 50;      //sets word wrapping on the body of the message to a given number of characters.
            $mail->IsHTML(true); //Sets message type to HTML
            $mail->Subject = 'Email Verification';   //Sets the Subject of the message
            $mail->Body = $mail_body;       //An HTML or plain text message body
            if ($mail->Send()) {        //Send an Email. Return true on success or false on error
                $message = '<label class="text-success">Register Done, Please check your mail.</label>';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
 <head>
    <title>OIMS Registration</title>  
    <script src="assets/js/jquery.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <script src="assets/js/bootstrap.min.js"></script>
    </head>
    <body>
        <br />
        <div class="container" style="width:100%; max-width:600px">
        <h2 align="center">OIMS::Register</h2>
        <br />
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Register</h4></div>
            <div class="panel-body">
                <form method="post" id="register_form">
                <?php echo $message; ?>
        <div class="form-group">
            <label>User Name</label>
            <input type="text" name="user_name" class="form-control" pattern="[a-zA-Z ]+" required />
        </div>
        <div class="form-group">
            <label>User Email</label>
            <input type="email" name="user_email" class="form-control" required />
        </div>
        <div class="form-group">
            <input type="submit" name="register" id="register" value="Register" class="btn btn-info" />
        </div>
            </form>
     <p align="right">Already have an account? <a href="login.php">Login here</a></p>
    </div>
   </div>
  </div>
 </body>
</html>