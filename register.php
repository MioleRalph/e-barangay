<?php
// Display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection
include 'connection.php';

// Encryption functions
include 'encryption.php';

// Include SweetAlert2
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Function to send verification email
function sendEmail_verification($firstName, $email, $verification_token){
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();                                     
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;           
        $mail->Username   = 'maujo_malitbog@e-barangay.online';      
        $mail->Password   = 'barangayQ2001@';             
        $mail->SMTPSecure = 'ssl';   
        $mail->Port       = 465;              

        $mail->setFrom('maujo_malitbog@e-barangay.online', 'E-Barangay Maujo, Malitbog');
        $mail->addAddress($email, $firstName);

        $email_template = "
            <div style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 40px 0;'>
                <table align='center' width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);'>
                    <tr>
                        <td style='background: #4e73df; padding: 24px 0; border-radius: 8px 8px 0 0; text-align: center;'>
                            <img src='https://e-barangay.online/components/img/stock_image/brgy_logo_nobg.jpeg' alt='E-Barangay Logo' width='110' style='margin-bottom: 8px;'>
                            <h2 style='color: #fff; margin: 0; font-size: 24px;'>E-Barangay Malitbog</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 32px 30px 24px 30px; color: #333;'>
                            <h3 style='margin-top: 0;'>Hello, $firstName!</h3>
                            <p style='font-size: 16px; line-height: 1.6;'>
                                Thank you for registering with <strong>E-Barangay Maujo, Malitbog</strong>.<br>
                                To complete your registration, please verify your email address by clicking the button below:
                            </p>
                            <div style='text-align: center; margin: 32px 0;'>
                                <a href='http://e-barangay.online/verify_email.php?token=$verification_token'
                                   style='background: #4e73df; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 5px; font-size: 16px; display: inline-block;'>
                                    Verify Email Address
                                </a>
                            </div>
                            <p style='font-size: 14px; color: #888;'>
                                If you did not create an account, please ignore this email.<br>
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

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address - E-Barangay Maujo, Malitbog';
        $mail->Body    = $email_template;
        $mail->AltBody = "Hello $firstName,\n\nThank you for registering with E-Barangay Maujo, Malitbog.\nPlease verify your email address by visiting the following link:\nhttp://e-barangay.online/verify_email.php?token=$verification_token\n\nIf you did not create an account, please ignore this email.";

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Directory for uploaded images
$uploadDirectory = __DIR__ . '/uploads/';
if (!is_writable($uploadDirectory)) {
    die("Directory is not writable: $uploadDirectory");
}

$warning_msg = [];
$success_msg = [];

if (isset($_POST['submit'])) {

    // Uploaded file
    $file = $_FILES['image'];

    // Encrypt sensitive fields
    $firstName = encryptData($_POST['f_name']);
    $lastName = encryptData($_POST['l_name']);
    $dob = ($_POST['dob']);
    $purok = encryptData($_POST['purok']);
    $contact_number = encryptData($_POST['contact_number']);

    $email = $_POST['email'];
    $account_type = 2;
    $approval_status = 'pending';
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $c_pass = password_verify($_POST['confirm_password'], $pass);

    $verification_token = md5(rand());
    $verification_status = 'not verified';

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif' ,'image/webp' ,'image/jpg']; 
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        die('Invalid file type. Only JPEG, PNG, WEBP, and GIF are allowed.');
    }

    // Move uploaded file
    $fileName = uniqid('profile_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDirectory . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        die('Failed to upload the image. Please try again.');
    }

    $user_id = bin2hex(random_bytes(8)); // 16-character hexadecimal

    // Check if email already exists
    $verify_email = $connection->prepare("SELECT * FROM `accounts` WHERE `email` = ?");
    $verify_email->execute([$email]);

    if ($verify_email->rowCount() > 0) {
        $warning_msg[] = 'Email already taken!';
    } else {
        if ($c_pass == 1) {
            // Insert encrypted user data
            $insert_user = $connection->prepare("INSERT INTO `accounts`(`account_id`, `profile_pic`, `first_name`, `last_name`, `date_of_birth`, `purok`, `contact_number`, `email`, `password`, `user_type`, `verification_token`, `verification_status`, `approval_status`, `date_registered`) 
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insert_user->execute([$user_id, $fileName, $firstName, $lastName, $dob, $purok, $contact_number, $email, $pass, $account_type, $verification_token, $verification_status, $approval_status]);

            sendEmail_verification($_POST['f_name'], $email, $verification_token); // Send plaintext first name in email
            $success_msg[] = 'Register successful. Check your email for verification!';
            
            echo '<script>setTimeout(function() { window.location.href = "login.php"; }, 200);</script>';
        } else {
            $warning_msg[] = 'Confirm password not matched!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Barangay - Register</title>

    <!-- Fonts & CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="components/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Barangay System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item active"><a class="nav-link" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>

                            <form class="user" action="register.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload Profile Picture</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image" id="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                </div>
                                <script>
                                    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
                                        var fileName = e.target.files[0]?.name || 'Choose file';
                                        e.target.nextElementSibling.innerText = fileName;
                                    });
                                </script>

                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="f_name" name="f_name" placeholder="First Name">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="l_name" name="l_name" placeholder="Last Name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="date" class="form-control form-control-user" id="dob" name="dob">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="purok" name="purok" placeholder="Purok">
                                </div>

                                <div class="form-group">
                                    <input type="text" pattern="\d{11}" maxlength="11" minlength="11" class="form-control form-control-user" id="contact_number" name="contact_number" placeholder="Contact Number (11 digits)" title="Contact number must be exactly 11 digits" required>
                                </div>

                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Email Address">
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user" id="confirm_password" name="confirm_password" placeholder="Repeat Password">
                                    </div>
                                </div>

                                <div class="input-box button">
                                    <input type="Submit" name="submit" value="Register Account" class="btn btn-primary btn-user btn-block">
                                </div>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.php">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        foreach($warning_msg as $msg){
            echo "<script>Swal.fire('Warning!', '$msg', 'warning');</script>";
        }
        foreach($success_msg as $msg){
            echo "<script>Swal.fire('Success!', '$msg', 'success');</script>";
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="components/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="components/js/sb-admin-2.min.js"></script>
</body>
</html>
