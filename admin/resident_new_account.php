<?php

    include '../includes/admin/admin_sidebar.php';

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
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;           
            $mail->Username   = 'ralphmiole2001@gmail.com';      
            $mail->Password   = 'avpc xhnd qlxe jbqk';             
            $mail->SMTPSecure = 'ssl';   
            $mail->Port       = 465;              

            //Recipients
            $mail->setFrom('ralphmiole2001@gmail.com', $firstName);
            $mail->addAddress($email); 

            $email_template = "
                <h5>Hi $firstName,</h5>
                <p>Click the link below to verify your email address</p>
                <br><br>
                <a href='http://localhost/e-barangay/verify_email.php?token=$verification_token'>CLICK HERE</a>
            ";
            
            //Content
            $mail->isHTML(true);                            
            $mail->Subject = 'This is email verification';

            $mail->Body    = $email_template;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

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
        $email = $_POST['email'];
        $account_type = $_POST['account_type'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

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

        // Validate the password confirmation
        if (empty($_POST['password']) || empty($_POST['confirm_password']) || $_POST['password'] !== $_POST['confirm_password']) {
            die('Password and Confirm Password do not match.');
        }

        // Check if the email already exists
        $verify_email = $connection->prepare("SELECT * FROM `accounts` WHERE `email` = ?");
        $verify_email->execute([$email]);

        if ($verify_email->rowCount() > 0) {
            $warning_msg[] = 'Email already taken!';
        } else {
            // Insert the new account into the database
            $insert_user = $connection->prepare("INSERT INTO `accounts`(`account_id`, `profile_pic`, `first_name`, `last_name`, `email`, `password`, `user_type`, `verification_token`, `verification_status`, `date_registered`) 
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insert_user->execute([$user_id, $fileName, $firstName, $lastName, $email, $pass, $account_type, $verification_token, $verification_status]);

            // Send verification email
            sendEmail_verification($firstName, $email, $verification_token);
            $success_msg[] = 'Register successful. Check your email for verification!';

            // Redirect after successful registration
            echo '<script>setTimeout(function() { window.location.href = "official_new_account.php"; }, 0);</script>';
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