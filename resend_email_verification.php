<?php
    include 'connection.php';  
    session_start();
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';


    function resend_verification($name, $email, $verification_token){

        $mail = new PHPMailer(true);

        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                    
            $mail->isSMTP();                                     
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;           
            $mail->Username   = 'maujo_malitbog@e-barangay.online';      
            $mail->Password   = 'barangayQ2001@';             
            $mail->SMTPSecure = 'ssl';   
            $mail->Port       = 465;              

            //Recipients
            $mail->setFrom('maujo_malitbog@e-barangay.online', 'E-Barangay Maujo, Malitbog');
            $mail->addAddress($email);

            // Extract first name for personalization
            $firstName = explode(' ', trim($name))[0];

            $email_template = "
                <div style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 40px 0;'>
                    <table align='center' width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);'>
                        <tr>
                            <td style='background: #4e73df; padding: 24px 0; border-radius: 8px 8px 0 0; text-align: center;'>
                                <img src='https://e-barangay.online/components/img/stock_image/brgy_logo_nobg.jpeg' alt='E-Barangay Logo' width='110' style='margin-bottom: 8px;'>
                                <h2 style='color: #fff; margin: 0; font-size: 24px;'>E-Barangay Maujo, Malitbog</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 32px 30px 24px 30px; color: #333;'>
                                <h3 style='margin-top: 0;'>Hello, $firstName!</h3>
                                <p style='font-size: 16px; line-height: 1.6;'>
                                    You requested to resend your email verification for <strong>E-Barangay Maujo, Malitbog</strong>.<br>
                                    To activate your account, please verify your email address by clicking the button below:
                                </p>
                                <div style='text-align: center; margin: 32px 0;'>
                                    <a href='http://e-barangay.online/verify_email.php?token=$verification_token'
                                       style='background: #4e73df; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 5px; font-size: 16px; display: inline-block;'>
                                        Verify Email Address
                                    </a>
                                </div>
                                <p style='font-size: 14px; color: #888;'>
                                    If you did not request this, you can safely ignore this email.<br>
                                    This link will expire after 24 hours for your security.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style='background: #f4f6f8; padding: 18px 30px; border-radius: 0 0 8px 8px; text-align: center; color: #aaa; font-size: 13px;'>
                                &copy; " . date('Y') . " E-Barangay Maujo, Malitbog. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </div>
            ";

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Resend Email Verification - E-Barangay Maujo, Malitbog';
            $mail->Body    = $email_template;
            $mail->AltBody = "Hello $firstName,\n\nYou requested to resend your email verification for E-Barangay Maujo, Malitbog.\nPlease verify your email address by visiting the following link:\nhttp://e-barangay.online/verify_email.php?token=$verification_token\n\nIf you did not request this, you can ignore this email.";
            
            $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_verification'])) {

        if(!empty($_POST['email'])){
            $email = $_POST['email'];

            $check_email = $connection->prepare("SELECT * FROM `accounts` WHERE  `email` = ? LIMIT 1");
            $check_email->execute([$email]);

            if($check_email->rowCount() > 0){
                $row = $check_email->fetch(PDO::FETCH_ASSOC);

                if($row['verification_status'] == "not verified"){

                    $name = $row['first_name'] . " " . $row['last_name'];
                    $email = $row['email'];
                    $verification_token = $row['verification_token'];

                    resend_verification($name, $email, $verification_token);
                    
                    $_SESSION['status'] = "Verification has been sent to your email.";
                    header('Location: login.php');
                    exit;
                }
                else{
                    $_SESSION['status'] = "Email already verified, please login";
                    header('Location: login.php');
                    exit;
                }
            }

            else{
                $_SESSION['status'] = "Email is not registered. please register first.";
                header('Location: register.php');
                exit;
            }
        }
        else{
            $_SESSION['status'] = "Please input your email";
            header('Location: resend_email_verification.php');
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

    <title>Barangay - Resend Email Verification</title>

    <!-- Fontawesom CDN link -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="components/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Barangay System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End of Navbar -->


    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">

                            <?php
                                if(isset($_SESSION['status'])){
                                    ?>
                                    <div class="alert alert-success">
                                        <h5><?= $_SESSION['status']?></h5>
                                    </div>
                                    <?php
                                    unset($_SESSION['status']);
                                }
                            ?>
                            <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Resend Email Verification</h1>
                                        <p class="mb-4">Enter your email address below, and we'll send you a new verification link to activate your account.</p>
                                    </div>
                                    <form class="user" action="resend_email_verification.php" method="POST">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="email" name="email" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." required>
                                        </div>
                                        <button type="submit" name="resend_verification" class="btn btn-primary btn-user btn-block">
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