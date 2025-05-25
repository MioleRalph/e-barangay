<?php
    include '../includes/official/official_sidebar.php';

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';


    $uploadDirectory = __DIR__ . '/../uploads/'; // Absolute path to 'uploads' directory

    // Check if the 'uploads' directory is writable. This is important to ensure we can upload files to it.
    if (!is_writable($uploadDirectory)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Directory is not writable: $uploadDirectory'
            });
        </script>";
        exit();
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['announcement_title'];
        $content = $_POST['announcement_content'];
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

        // Check if the file is uploaded and handle errors
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $fileType = mime_content_type($file['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type',
                        text: 'Only images (JPEG, PNG, WEBP, GIF) and documents (PDF, DOC, DOCX, XLS, XLSX) are allowed.'
                    });
                </script>";
                exit();
            }

            $fileName = uniqid('announcement_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $filePath = $uploadDirectory . $fileName;

            // Move the uploaded file to the target directory
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: 'Failed to upload the file. Please try again.'
                    });
                </script>";
                exit();
            }

            $attachment = $fileName;
        }

        // Insert the new announcement details into the database
        $insert_announcement = $connection->prepare("INSERT INTO `announcements` (`title`, `content`, `category`, `posted_by`, `audience`, `status`, `created_at`, `updated_at`, `attachment`) 
                                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_announcement->execute([$title, $content, $category, $user_id, $audience, $status, $createdAt, $updatedAt, $attachment]);

        // Show success message and redirect
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Announcement Added',
                text: 'The announcement has been successfully added.'
            }).then(() => {
                window.location.href = 'add_announcements.php';
            });
        </script>";
        exit();
    }
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header text-center">
            <h5>Add Announcement</h5>
        </div>
        <div class="card-body">
            <form action="add_announcements.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="announcement_title" class="form-label">Title</label>

                    <div class="form-group">
                        <input type="text" class="form-control" id="announcement_title" name="announcement_title" placeholder="Enter title" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="announcement_content" class="form-label">Content</label>

                    <div class="form-group">
                        <textarea class="form-control" id="announcement_content" name="announcement_content" rows="4" placeholder="Enter content" required></textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="announcement_category" class="form-label">Category</label>

                    <div class="form-group">
                        <select class="form-control" name="announcement_category" id="announcement_category" required>
                            <option value="" disabled selected>Select category</option>
                            <option value="Events">Events</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Health">Health</option>
                            <option value="Public Notice">Public Notice</option>
                            <option value="Lost & Found">Lost & Found</option>
                            <option value="Job Postings">Job Postings</option>
                            <option value="Community Projects">Community Projects</option>
                        </select>
                    </div>

                </div>

                <div class="mb-3">
                    <label for="announcement_audience" class="form-label">Audience</label>

                    <div class="form-group">
                        <select class="form-control" name="announcement_audience" id="cannouncement_audience" required>
                            <option value="" disabled selected>Select audience</option>
                            <option value="All">All</option>
                            <option value="Residents">Residents</option>
                            <option value="Officials">Officials</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="announcement_status" class="form-label">Status</label>

                    <div class="form-group">
                        <select class="form-control" name="announcement_status" id="announcement_status" required>
                            <option value="" disabled selected>Select status</option>
                            <option value="Active">Active</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-group">
                        <label for="image" class="form-label">Upload Attachment</label>
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
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Submit Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>