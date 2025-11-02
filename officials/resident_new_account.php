<?php
    include '../includes/official/official_sidebar.php';

    // PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require '../vendor/autoload.php';


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
     $uploadDirectory = __DIR__ . '/../uploads/'; // Absolute path to 'images' directory

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
    $approval_status = 'pending';
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $verification_token = md5(rand());
    $verification_status = 'not verified';

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg']; 
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) die('Invalid file type.');

    $fileName = uniqid('profile_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . $fileName)) {
        die('Failed to upload image.');
    }

    $user_id = bin2hex(random_bytes(8)); // 16-character hex

    // Password validation
    if (empty($_POST['password']) || empty($_POST['confirm_password']) || $_POST['password'] !== $_POST['confirm_password']) {
        die('Password and Confirm Password do not match.');
    }

    // Check email uniqueness
    $verify_email = $connection->prepare("SELECT * FROM `accounts` WHERE `email` = ?");
    $verify_email->execute([$email]);

    if ($verify_email->rowCount() > 0) {
        $warning_msg[] = 'Email already taken!';
    } else {
        // Encrypt sensitive data
        $encrypted_firstName = encryptData($firstName);
        $encrypted_lastName = encryptData($lastName);
        $encrypted_purok = encryptData($purok);
        $encrypted_contact = encryptData($contact_number);

        // Insert account
        $insert_user = $connection->prepare("INSERT INTO `accounts`
            (`account_id`, `profile_pic`, `first_name`, `last_name`, `date_of_birth`, `purok`, `contact_number`, `email`, `password`, `user_type`, `approval_status`, `verification_token`, `verification_status`, `date_registered`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $insert_user->execute([
            $user_id,
            $fileName,
            $encrypted_firstName,
            $encrypted_lastName,
            $dob,
            $encrypted_purok,
            $encrypted_contact,
            $email,
            $pass,
            $account_type,
            $approval_status,
            $verification_token,
            $verification_status
        ]);

        // Send verification email
        sendEmail_verification($firstName, $email, $verification_token);
        $success_msg[] = 'Register successful. Check your email for verification!';
        echo '<script>setTimeout(function() { window.location.href = "resident_new_account.php"; }, 0);</script>';
    }
}
?>

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Register New Resident Account</h4>
        </div>
        <div class="card-body p-4">
            <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" id="account_type" name="account_type" value="2" />
                <!-- Profile Picture Upload -->
                <div class="form-group mb-4 text-center">
                    <label for="image" class="form-label font-weight-bold">Profile Picture</label>
                    <div class="d-flex justify-content-center">
                        <div class="custom-file w-75">
                            <input type="file" class="custom-file-input" name="image" id="image" accept="image/*" required>
                            <label class="custom-file-label" for="image">Choose file</label>
                        </div>
                    </div>
                </div>
                <script>
                    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
                        var fileName = e.target.files[0]?.name || 'Choose file';
                        e.target.nextElementSibling.innerText = fileName;
                    });
                </script>

                <div class="form-row mb-3">
                    <div class="form-group col-md-6">
                        <label for="f_name" class="font-weight-bold">First Name</label>
                        <input type="text" class="form-control" id="f_name" name="f_name" placeholder="Enter First Name" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="l_name" class="font-weight-bold">Last Name</label>
                        <input type="text" class="form-control" id="l_name" name="l_name" placeholder="Enter Last Name" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="dob" class="font-weight-bold">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                </div>

                <div class="form-group mb-3">
                    <label for="purok" class="font-weight-bold">Purok</label>
                    <input type="text" class="form-control" id="purok" name="purok" placeholder="Enter Purok" required>
                </div>

                <div class="form-group mb-3">
                    <label for="contact_number" class="font-weight-bold">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Enter Contact Number" required pattern="\d{11}" maxlength="11" minlength="11" title="Contact number must be exactly 11 digits">
                </div>
                
                <div class="form-group mb-3">
                    <label for="email" class="font-weight-bold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                </div>

                <div class="form-row mb-4">
                    <div class="form-group col-md-6">
                        <label for="password" class="font-weight-bold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required minlength="6">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="confirm_password" class="font-weight-bold">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-block font-weight-bold py-2">
                    Register Account
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>