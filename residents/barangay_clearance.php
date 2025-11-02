<?php 
    include '../includes/resident/resident_sidebar.php'; 
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    // Import PHPMailer classes
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Include PHPMailer files
    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';

    // Function to send email notifications to officials and admins
    function sendEmail_notification($recipientName, $recipientEmail, $residentName, $residentEmail, $refNumber) {
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();                                     
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;           
            $mail->Username   = 'maujo_malitbog@e-barangay.online';      
            $mail->Password   = 'barangayQ2001@';             
            $mail->SMTPSecure = 'ssl';   
            $mail->Port       = 465;              

            // Sender and recipient
            $mail->setFrom('maujo_malitbog@e-barangay.online', 'E-Barangay Maujo, Malitbog');
            $mail->addAddress($recipientEmail, $recipientName);

            // Email content
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
                                <h3 style='margin-top: 0;'>New Barangay Clearance Request</h3>
                                <p style='font-size: 16px; line-height: 1.6;'>
                                    Resident <strong>$residentName</strong> (<a href='mailto:$residentEmail'>$residentEmail</a>) has submitted a new
                                    <strong>Barangay Clearance</strong> request.<br><br>
                                    Reference Number: <strong>$refNumber</strong><br>
                                    Please review and process the request in your E-Barangay dashboard.
                                </p>
                                <div style='text-align: center; margin: 32px 0;'>
                                    <a href='https://e-barangay.online/admin_login.php' 
                                    style='background: #4e73df; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 5px; font-size: 16px; display: inline-block;'>
                                        Go to Dashboard
                                    </a>
                                </div>
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

            // Mail settings
            $mail->isHTML(true);
            $mail->Subject = "New Barangay Clearance Request - $residentName";
            $mail->Body    = $email_template;
            $mail->AltBody = "Resident $residentName ($residentEmail) submitted a Barangay Clearance request. Ref#: $refNumber";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email could not be sent to $recipientEmail. Error: {$mail->ErrorInfo}");
        }
    }

    // Fetch logged-in resident account info
    $select_account = $connection->prepare("SELECT * FROM `accounts` WHERE account_id = ? LIMIT 1");
    $select_account->execute([$user_id]);
    $account = $select_account->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['submit'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $amount = 100; 
        $status = 'Pending';
        $request_type = 'Barangay Clearance';
        $ref_number = $_POST['ref_number'];

        // Insert request into database
        $insert = $connection->prepare("INSERT INTO `file_request` 
            (`user_id`, `name`, `date_of_birth`, `email`, `amount`, `transaction_type`, `transaction_status`, `date_submitted`, `ref_number`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $insert->execute([$user_id, $full_name, $dob, $email, $amount, $request_type, $status, $ref_number]);

        // Log the request
        $log_stmt = $connection->prepare("INSERT INTO `resident_request_logs` 
            (`account_id`, `name`, `activity`, `activity_type`, `timestamp`) 
            VALUES (?, ?, ?, ?, NOW())");
        $log_stmt->execute([$user_id, $full_name, 'Requested a Barangay Clearance', 'Barangay Clearance']);

        // Create official notification
        $notification_message = "New Barangay Clearance request submitted by " . strtoupper($full_name) . 
            " Email: " . strtoupper($email) . ". Please review and process the request.";
        $insert_notification = $connection->prepare("INSERT INTO `official_notifications` 
            (`resident_name`, `message`, `is_read`, `created_at`) VALUES (?, ?, '0', NOW())");
        $insert_notification->execute([$full_name, $notification_message]);

        // Send email notifications to all officials and admins (user_type 2 or 3)
        $officials_stmt = $connection->prepare("SELECT first_name, last_name, email FROM `accounts` WHERE user_type IN ('2', '3')");
        $officials_stmt->execute();
        $officials = $officials_stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($officials as $official) {
            $officialName = $official['first_name'] . ' ' . $official['last_name'];
            $officialEmail = $official['email'];
            sendEmail_notification($officialName, $officialEmail, $full_name, $email, $ref_number);
        }

        // SweetAlert success message
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Your Barangay Clearance request has been submitted successfully.',
                showConfirmButton: false,
                timer: 1800
            }).then(() => {
                window.location.href = 'barangay_clearance.php';
            });
        </script>";
    }
?>

<section>
    <div class="container py-5">
        <h1 class="text-center mb-4">Barangay Clearance</h1>
        <div class="row d-flex justify-content-center">
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="../components/img/undraw_profile_1.svg" alt="avatar"
                            class="rounded-circle img-fluid mb-3" style="width: 120px;">
                        <h5 class="mb-1"><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></h5>
                        <p class="text-muted mb-1"><?php echo ($account['email']); ?></p>
                        <p class="text-muted mb-2">
                            <?php echo isset($account['address']) ? ($account['address']) : 'No address provided'; ?>
                        </p>
                        <hr>
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="font-weight-bold mr-2">Total Amount:</span>
                            <span class="h5 mb-0 text-success">₱100</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="../components/gcash_qr.jpeg" alt="avatar"
                            class="img-fluid mb-3" style="width: 257px;">
                    </div>
                </div>
            </div>

            <div class="col-lg-10 d-flex justify-content-center">
                <div class="card mb-4" style="width: 100%; max-width: 1000px;">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <!-- Full Name -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="full_name" class="mb-0">Full Name</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                        value="<?php echo ($account['first_name'] . ' ' . $account['last_name']); ?>" required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="email" class="mb-0">Email</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" 
                                        value="<?php echo $account['email']; ?>" required>
                                </div>
                            </div>

                            <!-- Date of Birth -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="dob" class="mb-0">Date Of Birth</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="dob" name="dob" 
                                        value="<?php echo isset($account['date_of_birth']) ? ($account['date_of_birth']) : ''; ?>" required>
                                </div>
                            </div>

                            <!-- Reference Number -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="ref_number" class="mb-0">Reference Number</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ref_number" name="ref_number" required>
                                    <small id="refHelp" class="form-text text-muted">
                                        Enter the payment reference or GCash transaction number from your receipt or confirmation message.
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <button type="submit" name="submit" class="btn btn-primary btn-block font-weight-bold py-2 mt-4">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
