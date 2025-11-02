<?php
    include '../includes/official/official_sidebar.php';
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';

    // Function: Send email to resident about status change
    function sendBarangayClearanceEmail($residentName, $residentEmail, $status)
    {
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
            $mail->addAddress($residentEmail, $residentName);

            if (strtolower($status) === 'approved') {
                $subject = "Your Barangay Clearance Request Has Been Approved";
                $message = "
                    <h3>Good news, $residentName!</h3>
                    <p>Your request for a <strong>Barangay Clearance</strong> has been <strong>approved</strong>.</p>
                    <p>Please visit the barangay office or check your E-Barangay account for further instructions.</p>
                ";
            } else if (strtolower($status) === 'rejected') {
                $subject = "Your Barangay Clearance Request Has Been Rejected";
                $message = "
                    <h3>Hello $residentName,</h3>
                    <p>We regret to inform you that your request for a <strong>Barangay Clearance</strong> has been <strong>rejected</strong>.</p>
                    <p>Please contact the barangay office for assistance or re-application.</p>
                ";
            } else {
                return;
            }

            $email_template = "
                <div style='font-family:Arial,sans-serif;background:#f4f6f8;padding:40px 0;'>
                    <table align='center' width='100%' cellpadding='0' cellspacing='0' 
                    style='max-width:600px;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.05);'>
                        <tr>
                            <td style='background:#4e73df;padding:24px 0;border-radius:8px 8px 0 0;text-align:center;'>
                                <img src='https://e-barangay.online/components/img/stock_image/brgy_logo_nobg.jpeg' alt='E-Barangay Logo' width='110'>
                                <h2 style='color:#fff;margin:0;'>E-Barangay Maujo, Malitbog</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:32px 30px;color:#333;font-size:15px;line-height:1.6;'>$message</td>
                        </tr>
                        <tr>
                            <td style='background:#f4f6f8;padding:18px 30px;border-radius:0 0 8px 8px;text-align:center;color:#aaa;font-size:13px;'>
                                &copy; " . date('Y') . " E-Barangay Maujo, Malitbog. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </div>
            ";

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $email_template;
            $mail->AltBody = strip_tags($message);

            $mail->send();
        } catch (Exception $e) {
            error_log("Email not sent to $residentEmail. Error: {$mail->ErrorInfo}");
        }
    }

    // Fetch all Barangay Clearance requests
    $query = $connection->prepare("SELECT * FROM `file_request` WHERE `transaction_type` = ?");
    $query->execute(['Barangay Clearance']);
    $barangay_clearance_requests = $query->fetchAll(PDO::FETCH_ASSOC);

    // Approve Request
    if (isset($_POST['approve_request'])) {
        $approve_id = $_POST['approve_id'];
        $verify = $connection->prepare("SELECT * FROM `file_request` WHERE id = ?");
        $verify->execute([$approve_id]);
        $request = $verify->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            echo "<script>Swal.fire({icon:'warning',title:'Not found',text:'Request not found.'});</script>";
            exit();
        }

        $update = $connection->prepare("UPDATE `file_request` SET `transaction_status` = 'Approved' WHERE id = ?");
        if ($update->execute([$approve_id])) {
            $approved_id = $user_id ?? null;
            if (empty($approved_id)) {
                echo "<script>Swal.fire({icon:'error',title:'Error',text:'Official ID missing.'});</script>";
                exit();
            }

            $activity_text = 'Barangay Clearance Approved';
            $encrypted_activity = encryptData($activity_text);

            $log = $connection->prepare("INSERT INTO `official_requests_logs`
                (`approved_id`, `resident_id`, `resident_name`, `approved_by`, `activity`, `timestamp`)
                VALUES (?, ?, ?, ?, ?, NOW())");
            $log->execute([$approved_id, $request['user_id'], $request['name'], $_SESSION['full_name'], $encrypted_activity]);

            // Insert notification
            $notif = $connection->prepare("INSERT INTO `notifications`
                (`resident_id`, `message`, `is_read`, `resident_type`, `created_at`)
                VALUES (?, ?, '0', 'null', NOW())");
            $notif->execute([$request['user_id'], "Your Barangay Clearance request has been approved."]);

            // Send email
            sendBarangayClearanceEmail($request['name'], $request['email'], 'approved');

            echo "<script>
                Swal.fire({icon:'success',title:'Approved!',text:'Request has been approved.',showConfirmButton:false,timer:1500})
                .then(()=>{window.location.href='barangay_clearance.php';});
            </script>";
            exit();
        }
    }

    // Reject Request
    if (isset($_POST['reject_request'])) {
        $reject_id = $_POST['reject_id'];
        $verify = $connection->prepare("SELECT * FROM `file_request` WHERE id = ?");
        $verify->execute([$reject_id]);
        $request = $verify->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            echo "<script>Swal.fire({icon:'warning',title:'Not found',text:'Request not found.'});</script>";
            exit();
        }

        $update = $connection->prepare("UPDATE `file_request` SET `transaction_status` = 'Rejected' WHERE id = ?");
        if ($update->execute([$reject_id])) {
            $reject_official_id = $user_id ?? null;
            if (empty($reject_official_id)) {
                echo "<script>Swal.fire({icon:'error',title:'Error',text:'Official ID missing.'});</script>";
                exit();
            }

            $activity_text = 'Barangay Clearance Rejected';
            $encrypted_activity = encryptData($activity_text);

            $log = $connection->prepare("INSERT INTO `official_requests_logs`
                (`approved_id`, `resident_id`, `resident_name`, `approved_by`, `activity`, `timestamp`)
                VALUES (?, ?, ?, ?, ?, NOW())");
            $log->execute([$reject_official_id, $request['user_id'], $request['name'], $_SESSION['full_name'], $encrypted_activity]);

            // Insert notification
            $notif = $connection->prepare("INSERT INTO `notifications`
                (`resident_id`, `message`, `is_read`, `resident_type`, `created_at`)
                VALUES (?, ?, '0', 'null', NOW())");
            $notif->execute([$request['user_id'], "Your Barangay Clearance request has been rejected."]);

            // Send email
            sendBarangayClearanceEmail($request['name'], $request['email'], 'rejected');

            echo "<script>
                Swal.fire({icon:'success',title:'Rejected!',text:'Request has been rejected.',showConfirmButton:false,timer:1500})
                .then(()=>{window.location.href='barangay_clearance.php';});
            </script>";
            exit();
        }
    }
?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Barangay Clearance Requests</h1>
<p class="mb-4">
    Below is a list of all Barangay Clearance requests submitted by residents. You can review, approve, or reject each request directly from this table. For record-keeping, you may also download the activity logs as a PDF.
</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users"></i> Activity Logs
        </h6>
        <a href="../pdf/pdf_brgy_clearance.php" class="btn btn-primary btn-sm">
            <i class="fas fa-file-export"></i> PDF Download
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Date Of Birth</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Status</th>
                        <th>Reference #</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                    <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Date Of Birth</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Status</th>
                        <th>Reference #</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($barangay_clearance_requests as $logs):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo ($logs['name']); ?></td>
                            <td><?php echo ($logs['user_id']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($logs['date_of_birth'])); ?></td>
                            <td><?php echo ($logs['email']); ?></td>
                            <td><?php echo ($logs['amount']); ?></td>
                            <td><?php echo ($logs['transaction_type']); ?></td>
                            <td><?php echo ($logs['transaction_status']); ?></td>
                            <td><?php echo ($logs['ref_number']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($logs['date_submitted'])); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="approve_id" value="<?php echo $logs['id']; ?>">
                                        <input type="hidden" name="approve_request" value="1">
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="reject_id" value="<?php echo $logs['id']; ?>">
                                        <input type="hidden" name="reject_request" value="1">
                                        <button type="submit" class="btn btn-warning btn-sm" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
