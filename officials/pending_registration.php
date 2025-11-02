<?php
include '../includes/official/official_sidebar.php';
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Function to send email notification to resident
function sendAccountStatusEmail($recipientName, $recipientEmail, $status)
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
        $mail->addAddress($recipientEmail, $recipientName);

        // Customize email message based on status
        $subject = '';
        $message = '';

        switch (strtolower($status)) {
            case 'approved':
                $subject = "Your E-Barangay Account Has Been Approved";
                $message = "
                    <h3>Good news, $recipientName!</h3>
                    <p>Your E-Barangay account has been <strong>approved</strong> by the barangay officials.</p>
                    <p>You can now log in and start using the E-Barangay portal.</p>
                    <a href='https://e-barangay.online/login.php' 
                    style='background:#4e73df;color:#fff;text-decoration:none;padding:12px 24px;border-radius:5px;'>Log in to your account</a>
                ";
                break;

            case 'rejected':
                $subject = "Your E-Barangay Account Has Been Rejected";
                $message = "
                    <h3>Hello $recipientName,</h3>
                    <p>We regret to inform you that your E-Barangay account registration has been <strong>rejected</strong>.</p>
                    <p>Please contact your barangay office for more information or to reapply.</p>
                ";
                break;

            case 'deleted':
                $subject = "Your E-Barangay Account Has Been Deleted";
                $message = "
                    <h3>Dear $recipientName,</h3>
                    <p>Your E-Barangay account has been <strong>deleted</strong> from our system.</p>
                    <p>If this was a mistake or you have questions, please reach out to the barangay office.</p>
                ";
                break;
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
        error_log("Email could not be sent to $recipientEmail. Error: {$mail->ErrorInfo}");
    }
}

// Fetch pending, approved, and rejected accounts
$query = $connection->prepare("
    SELECT `account_id`, `first_name`, `last_name`, `date_of_birth`, `email`, `date_registered`, `approval_status`
    FROM `accounts`
    WHERE `approval_status` IN (?,?,?)
    AND `user_type` = 2
    ORDER BY
        CASE `approval_status`
            WHEN 'pending' THEN 1
            WHEN 'rejected' THEN 2
            WHEN 'approved' THEN 3
            ELSE 4
        END,
        `date_registered` DESC
");
$query->execute(['pending', 'rejected', 'approved']);
$pending_registration = $query->fetchAll(PDO::FETCH_ASSOC);

// Approve account
if (isset($_POST['approve_request'])) {
    $approve_id = $_POST['approve_id'];

    $verify = $connection->prepare("SELECT * FROM `accounts` WHERE `account_id` = ?");
    $verify->execute([$approve_id]);
    $account = $verify->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        echo "<script>Swal.fire({icon:'warning',title:'Not found',text:'Account not found.'});</script>";
        exit();
    }

    $update = $connection->prepare("UPDATE `accounts` SET `approval_status`='approved' WHERE `account_id`=?");
    if ($update->execute([$approve_id])) {
        sendAccountStatusEmail((decryptData($account['first_name'])), $account['email'], 'approved');
        echo "<script>
            Swal.fire({icon:'success',title:'Approved',text:'Account has been approved.',showConfirmButton:false,timer:1500})
            .then(()=>{window.location.href='pending_registration.php';});
        </script>";
    }
    exit();
}

// Reject account
if (isset($_POST['reject_request'])) {
    $reject_id = $_POST['reject_id'];

    $verify = $connection->prepare("SELECT * FROM `accounts` WHERE `account_id` = ?");
    $verify->execute([$reject_id]);
    $account = $verify->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        echo "<script>Swal.fire({icon:'warning',title:'Not found',text:'Account not found.'});</script>";
        exit();
    }

    $update = $connection->prepare("UPDATE `accounts` SET `approval_status`='rejected' WHERE `account_id`=?");
    if ($update->execute([$reject_id])) {
        sendAccountStatusEmail((decryptData($account['first_name'])), $account['email'], 'rejected');
        echo "<script>
            Swal.fire({icon:'success',title:'Rejected',text:'Account has been rejected.',showConfirmButton:false,timer:1500})
            .then(()=>{window.location.href='pending_registration.php';});
        </script>";
    }
    exit();
}

// Delete account
if (isset($_POST['delete_request'])) {
    $delete_id = $_POST['delete_id'];

    $verify = $connection->prepare("SELECT * FROM `accounts` WHERE `account_id` = ?");
    $verify->execute([$delete_id]);
    $account = $verify->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        echo "<script>Swal.fire({icon:'warning',title:'Not found',text:'Account not found.'});</script>";
        exit();
    }

    $delete = $connection->prepare("DELETE FROM `accounts` WHERE `account_id`=?");
    if ($delete->execute([$delete_id])) {
        sendAccountStatusEmail((decryptData($account['first_name'])), $account['email'], 'deleted');
        echo "<script>
            Swal.fire({icon:'success',title:'Deleted',text:'Account has been deleted.',showConfirmButton:false,timer:1500})
            .then(()=>{window.location.href='pending_registration.php';});
        </script>";
    }
    exit();
}
?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Account Registration List</h1>
<p class="mb-4">
    Below is a list of account registration requests submitted by residents. Review each submission and approve or reject accounts as needed.
</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users"></i> Registration List
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Date Of Birth</th>
                        <th>Email</th>
                        <th>Timestamp</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                    <th>#</th>
                        <th>Name</th>
                        <th>Date Of Birth</th>
                        <th>Email</th>
                        <th>Date Registered</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($pending_registration as $pending):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo (decryptData($pending['first_name'])) . ' ' . (decryptData($pending['last_name'])); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($pending['date_of_birth'])); ?></td>
                            <td><?php echo ($pending['email']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($pending['date_registered'])); ?></td>
                            <?php
                                $status = strtolower($pending['approval_status'] ?? 'pending');
                                switch ($status) {
                                    case 'approved':
                                        $badge = 'badge-success';
                                        break;
                                    case 'rejected':
                                        $badge = 'badge-danger';
                                        break;
                                    case 'pending':
                                    default:
                                        $badge = 'badge-warning';
                                        break;
                                }
                                $label = ucfirst($status);
                            ?>
                            <td>
                                <span class="badge <?php echo $badge; ?> text-uppercase">
                                    <?php echo ($label); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="approve_id" value="<?php echo $pending['account_id']; ?>">
                                        <input type="hidden" name="approve_request" value="1">
                                        <button type="button" class="btn btn-success btn-sm" title="Approve"
                                            onclick="(function(btn){
                                                Swal.fire({
                                                    title: 'Approve this account?',
                                                    text: 'This will approve the account and make it active.',
                                                    icon: 'question',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#28a745',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Yes, approve',
                                                    reverseButtons: true
                                                }).then(function(result){
                                                    if (result.isConfirmed) {
                                                        btn.closest('form').submit();
                                                    }
                                                });
                                            })(this);">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="reject_id" value="<?php echo $pending['account_id']; ?>">
                                        <input type="hidden" name="reject_request" value="1">
                                        <button type="button" class="btn btn-warning btn-sm" title="Reject"
                                            onclick="(function(btn){
                                                Swal.fire({
                                                    title: 'Reject this account?',
                                                    text: 'This will mark the account as rejected.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Yes, reject',
                                                    reverseButtons: true
                                                }).then(function(result){
                                                    if (result.isConfirmed) {
                                                        btn.closest('form').submit();
                                                    }
                                                });
                                            })(this);">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="delete_id" value="<?php echo $pending['account_id']; ?>">
                                        <input type="hidden" name="delete_request" value="1">
                                        <button type="button" class="btn btn-danger btn-sm" title="Delete"
                                            onclick="(function(btn){
                                                Swal.fire({
                                                    title: 'Delete this account?',
                                                    text: 'This action cannot be undone.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Yes, delete it!',
                                                    reverseButtons: true
                                                }).then(function(result){
                                                    if (result.isConfirmed) {
                                                        btn.closest('form').submit();
                                                    }
                                                });
                                            })(this);">
                                            <i class="fas fa-trash"></i>
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
