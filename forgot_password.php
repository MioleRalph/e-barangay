<?php
session_start();
include 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_password_reset($get_name, $get_email, $token)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ralphmiole2001@gmail.com'; // Replace with your Gmail address
        $mail->Password   = 'avpc xhnd qlxe jbqk';      // Replace with your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SMTPS encryption
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('ralphmiole2001@gmail.com', 'Password Reset');
        $mail->addAddress($get_email, $get_name); // Add recipient's email and name

        //Email content
        $email_template = "
                <h2>Password Reset Request</h2>
                <p>Hi $get_name,</p>
                <p>We received a request to reset your password. Click the link below to reset it:</p>

                // change localhost to your domain name or ip address 120.10.10.13
                <a href='http://localhost/e-barangay/password_reset.php?token=$token&email=$get_email'>Reset Password</a>
                <p>If you didn't request this, please ignore this email.</p>
            ";

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = $email_template;
        $mail->AltBody = 'Please use the HTML version of the email client to view this message.';

        $mail->send();
    } catch (Exception $e) {
        // Log the error or handle it gracefully
        error_log("Mailer Error: " . $mail->ErrorInfo);
        die("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {

    $email = $_POST['email'];
    $token = md5(rand());

    $check_email = $connection->prepare("SELECT * FROM `accounts` WHERE  `email` = ? LIMIT 1");
    $check_email->execute([$email]);

    if ($check_email->rowCount() > 0) {
        $row = $check_email->fetch(PDO::FETCH_ASSOC);

        $get_name = $row['first_name'] . " " . $row['last_name'];
        $get_email = $row['email'];

        $update_token = $connection->prepare("UPDATE `accounts` SET `verification_token` = ? WHERE `email` = ? LIMIT 1");
        $update_token->execute([$token, $get_email]);

        if ($update_token) {

            send_password_reset($get_name, $get_email, $token);

            $_SESSION['status'] = "reset password link has been sent to your email";
            header('Location: forgot_password.php');
            exit;
        } else {
            $_SESSION['status'] = "Something went wrong.";
            header('Location: forgot_password.php');
            exit;
        }
    } else {
        $_SESSION['status'] = "Email not found";
        header('Location: forgot_password.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Barangay - Forgot Password</title>

    <!-- Fontawesom CDN link -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="components/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <!-- Navbar -->
    <?php include 'includes/home_navbar.php'; ?>
    <!-- End of Navbar -->


    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">

                    <?php
                        if (isset($_SESSION['status'])) {
                        ?>
                            <div class="alert alert-success">
                                <h5><?= $_SESSION['status'] ?></h5>
                            </div>
                        <?php
                            unset($_SESSION['status']);
                        }
                    ?>
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image">
                                <img src="img/team/logo.webp" alt="logo" class="img-fluid">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                                        <p class="mb-4">We get it, stuff happens. Just enter your email address below
                                            and we'll send you a link to reset your password!</p>
                                    </div>
                                    <form class="user" action="forgot_password.php" method="POST">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address...">
                                        </div>
                                        <button type="submit" name="forgot_password" class="btn btn-primary btn-user btn-block">
                                            Resend Verification Email
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Already have an account? Login!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <?php include 'includes/scripts.php'; ?>