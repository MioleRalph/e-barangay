<?php
    // to display errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Start the session
    session_start();

    // database connection
    include 'connection.php';

    // Include SweetAlert2
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    // PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';


    function sendEmail_verification($firstName, $email, $verification_token){

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
            $mail->addAddress($email, $firstName);

            $email_template = "
                <div style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 40px 0;'>
                    <table align='center' width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);'>
                        <tr>
                            <td style='background: #4e73df; padding: 24px 0; border-radius: 8px 8px 0 0; text-align: center;'>
                                <img src='https://e-barangay.online/components/img/stock_image/brgy_logo.jpeg' alt='E-Barangay Logo' width='110' style='margin-bottom: 8px;'>
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

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email Address - E-Barangay Maujo, Malitbog';
            $mail->Body    = $email_template;
            $mail->AltBody = "Hello $firstName,\n\nThank you for registering with E-Barangay Maujo, Malitbog.\nPlease verify your email address by visiting the following link:\nhttp://e-barangay.online/verify_email.php?token=$verification_token\n\nIf you did not create an account, please ignore this email.";

            $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

     // Define the directory where uploaded images will be stored
     $uploadDirectory = __DIR__ . '/uploads/'; // Absolute path to 'images' directory

     // Check if the 'images' directory is writable. This is important to ensure we can upload files to it.
     if (!is_writable($uploadDirectory)) {
         die("Directory is not writable: $uploadDirectory");
     }


    $warning_msg = [];
    $success_msg = [];

    if (isset($_POST['submit'])) {

    
        $file = $_FILES['image'];
        $firstName = $_POST['f_name'];
        $lastName = $_POST['l_name'];
        $dob = $_POST['dob'];
        $purok = $_POST['purok'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $account_type = 2;
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $c_pass = password_verify($_POST['confirm_password'], $pass);

        $verification_token = md5(rand());
        $verification_status = 'not verified';

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif' ,'image/webp' ,'image/jpg']; 
        $fileType = mime_content_type($file['tmp_name']);
        echo "File Type: " . $fileType . "<br>";

        if (!in_array($fileType, $allowedTypes)) {
            die('Invalid file type. Only JPEG, PNG, WEBP, and GIF are allowed.');
        }

        $fileName = uniqid('profile_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDirectory . $fileName;

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            die('Failed to upload the image. Please try again.');
        }

        $user_id = bin2hex(random_bytes(8)); // Generates a 16-character hexadecimal string

        $verify_email = $connection->prepare("SELECT * FROM `accounts` WHERE `email` = ?");
        $verify_email->execute([$email]);

        if ($verify_email->rowCount() > 0) {
            $warning_msg[] = 'Email already taken!';
        } 
        
        else {
            
            if ($c_pass == 1) {

                    $insert_user = $connection->prepare("INSERT INTO `accounts`(`account_id`,`profile_pic`, `first_name`, `last_name`, `date_of_birth`, `purok`, `contact_number`, `email`, `password`, `user_type`, `verification_token`, `verification_status`, `date_registered`) 
                                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $insert_user->execute([$user_id, $fileName, $firstName, $lastName, $dob, $purok, $contact_number, $email, $pass, $account_type, $verification_token, $verification_status]);

                sendEmail_verification("$firstName", "$email", "$verification_token");
                $success_msg[] = 'Register successful. Check your email for verification!';
                // Redirect after the alert is shown
                echo '<script>setTimeout(function() { window.location.href = "login.php"; }, 200);</script>';
            } 
            
            else {
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
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Barangay - Register</title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="components/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <!-- Navbar -->
        <?php include 'includes/home_navbar.php'; ?>
    <!-- End of Navbar -->

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>

                            <form class="user" action="register.php" method="POST" enctype="multipart/form-data">

                                <!-- Upload profile picture input -->
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload Profile Picture</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image" id="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                </div>
                                <script>
                                    // Update the label of the file input when a file is selected
                                    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
                                        var fileName = e.target.files[0]?.name || 'Choose file';
                                        e.target.nextElementSibling.innerText = fileName;
                                    });
                                </script>

                                <div class="form-group row">
                                    <!-- First name inpput -->
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="f_name" name="f_name"
                                            placeholder="First Name">
                                    </div>

                                    <!-- Last name input -->
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="l_name" name="l_name"
                                            placeholder="Last Name">
                                    </div>
                                </div>

                                <!-- Email input -->
                                <div class="form-group">
                                    <input type="date" class="form-control form-control-user" id="dob" name="dob">
                                </div>

                                <!-- Purok Input -->
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="purok" name="purok"
                                        placeholder="Purok">
                                </div>

                                <!-- Contact Number Input -->
                                <div class="form-group">
                                    <input type="text" pattern="\d{11}" maxlength="11" minlength="11" class="form-control form-control-user" id="contact_number" name="contact_number"
                                        placeholder="Contact Number (11 digits)" title="Contact number must be exactly 11 digits" required>
                                </div>

                                <!-- Email input -->
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="email" name="email"
                                        placeholder="Email Address">
                                </div>


                                <!-- Password input -->
                                <div class="form-group row">

                                    <!-- Password input -->
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            id="password" name="password" placeholder="Password">
                                    </div>

                                    <!-- Confirm password input -->
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            id="confirm_password" name="confirm_password" placeholder="Repeat Password">
                                    </div>
                                </div>
                                
                                <div class="input-box button">
                                    <input type="Submit" name="submit" value="Register Account" class="btn btn-primary btn-user btn-block">
                                </div>
                            </form>
                            <hr>

                            <div class="text-center">
                                <a class="small" href="forgot_password.php">Forgot Password?</a>
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

    <?php include 'includes/scripts.php'; ?>