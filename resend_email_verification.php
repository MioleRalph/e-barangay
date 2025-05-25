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
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;           
            $mail->Username   = 'ralphmiole2001@gmail.com';      
            $mail->Password   = 'avpc xhnd qlxe jbqk';             
            $mail->SMTPSecure = 'ssl';   
            $mail->Port       = 465;              

            //Recipients
            $mail->setFrom('ralphmiole2001@gmail.com', $name);
            $mail->addAddress($email); 

            $email_template = "
                <h2>You have registered</h2>
                <h5>Verify your email address to login with the link given below</h5>
                <br><br>
                <a href='http://localhost/e-barangay/verify_email.php?token=$verification_token'>CLICK HERE</a>
            ";
            
            //Content
            $mail->isHTML(true);                            
            $mail->Subject = 'Resend email verification';

            $mail->Body    = $email_template;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

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
    <?php include 'includes/home_navbar.php'; ?>
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