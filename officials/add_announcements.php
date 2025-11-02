<?php
include '../includes/official/official_sidebar.php';

echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Function to send email to users
function sendAnnouncementEmail($userName, $userEmail, $title, $content, $attachment = null) {
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
        $mail->addAddress($userEmail, $userName);

        $subject = "New Announcement: $title";
        $message = "
            <h3>Hello $userName,</h3>
            <p>There is a new announcement:</p>
            <h4>$title</h4>
            <p>$content</p>
        ";

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
        $mail->Body    = $email_template;
        $mail->AltBody = strip_tags($message);

        if ($attachment) {
            $mail->addAttachment(__DIR__ . '/../uploads/' . $attachment);
        }

        $mail->send();
    } catch (Exception $e) {
        error_log("Email not sent to $userEmail. Error: {$mail->ErrorInfo}");
    }
}

// File upload directory
$uploadDirectory = __DIR__ . '/../uploads/';
if (!is_writable($uploadDirectory)) {
    echo "<script>
        Swal.fire({icon:'error',title:'Error',text:'Directory is not writable: $uploadDirectory'});
    </script>";
    exit();
}

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Keep plain text for emails
    $plainTitle = $_POST['announcement_title'];
    $plainContent = $_POST['announcement_content'];

    // ✅ Encrypt only title and content for DB
    $title = encryptData($plainTitle);
    $content = encryptData($plainContent);

    // Other fields (not encrypted)
    $category = $_POST['announcement_category'];
    $audience = $_POST['announcement_audience'];
    $status = $_POST['announcement_status'];

    $createdAt = date('Y-m-d H:i:s');
    $updatedAt = date('Y-m-d H:i:s');
    $attachment = null;

    $allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 
        'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileType = mime_content_type($file['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>
                Swal.fire({icon:'error',title:'Invalid File Type',text:'Only images and documents are allowed.'});
            </script>";
            exit();
        }

        $fileName = uniqid('announcement_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDirectory . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            echo "<script>
                Swal.fire({icon:'error',title:'Upload Failed',text:'Failed to upload the file.'});
            </script>";
            exit();
        }

        $attachment = $fileName;
    }

    // ✅ Insert data (only title & content are encrypted)
    $insert_announcement = $connection->prepare("INSERT INTO `announcements` 
        (`title`, `content`, `category`, `posted_by`, `audience`, `status`, `created_at`, `updated_at`, `attachment`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_announcement->execute([
        $title, 
        $content, 
        $category, 
        $user_id, 
        $audience, 
        $status, 
        $createdAt, 
        $updatedAt, 
        $attachment
    ]);

    // Fetch recipients based on audience
    if ($audience === 'All') {
        $residentsQuery = $connection->query("SELECT `first_name`,`email` FROM `accounts` WHERE `user_type` IN (2,3)");
    } elseif ($audience === 'Officials') {
        $residentsQuery = $connection->query("SELECT `first_name`,`email` FROM `accounts` WHERE `user_type` = 3");
    } elseif ($audience === 'Residents') {
        $residentsQuery = $connection->query("SELECT `first_name`,`email` FROM `accounts` WHERE `user_type` = 2");
    }
    $residents = $residentsQuery->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Send emails using plain text
    foreach ($residents as $res) {
        sendAnnouncementEmail($res['first_name'], $res['email'], $plainTitle, $plainContent, $attachment);
    }

    // Success message
    echo "<script>
        Swal.fire({icon:'success',title:'Announcement Added',text:'The announcement has been successfully added.'})
        .then(()=>{window.location.href='add_announcements.php';});
    </script>";
    exit();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header text-center"><h5>Add Announcement</h5></div>
        <div class="card-body">
            <form action="add_announcements.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="announcement_title" class="form-label">Title</label>
                    <input type="text" class="form-control" name="announcement_title" id="announcement_title" placeholder="Enter title" required>
                </div>
                <div class="mb-3">
                    <label for="announcement_content" class="form-label">Content</label>
                    <textarea class="form-control" name="announcement_content" id="announcement_content" rows="4" placeholder="Enter content" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="announcement_category" class="form-label">Category</label>
                    <select class="form-control" name="announcement_category" id="announcement_category" required>
                        <option value="" disabled selected>Select category</option>
                        <option value="Events">Events</option>
                        <option value="Financial Assistance">Financial Assistance</option>
                        <option value="Emergency">Emergency</option>
                        <option value="Health">Health</option>
                        <option value="Public Notice">Public Notice</option>
                        <option value="Lost & Found">Lost & Found</option>
                        <option value="Job Postings">Job Postings</option>
                        <option value="Community Projects">Community Projects</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="announcement_audience" class="form-label">Audience</label>
                    <select class="form-control" name="announcement_audience" id="announcement_audience" required>
                        <option value="" disabled selected>Select audience</option>
                        <option value="All">All</option>
                        <option value="Residents">Residents</option>
                        <option value="Officials">Officials</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="announcement_status" class="form-label">Status</label>
                    <select class="form-control" name="announcement_status" id="announcement_status" required>
                        <option value="" disabled selected>Select status</option>
                        <option value="Active">Active</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Upload Attachment</label>
                    <input type="file" class="form-control" name="image" id="image" 
                    accept="image/*,application/pdf,application/msword,
                    application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                    application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Submit Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
