<?php
    include '../includes/official/official_sidebar.php';
    $announcements_list = $connection->query("SELECT * FROM `announcements` WHERE `category` = 'Community Projects'")->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    $uploadDirectory = __DIR__ . '/../uploads/'; // Absolute path to 'uploads' directory

    // Check if the 'uploads' directory is writable
    if (!is_writable($uploadDirectory)) {
        echo "<script>Swal.fire('Error', 'Directory is not writable: $uploadDirectory', 'error');</script>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_announcement']) && $_POST['delete_announcement'] == 1) {
            $announcementId = $_POST['announcement_id'];

            // Fetch the existing attachment to delete it from the server
            $existingAnnouncement = $connection->query("SELECT `attachment` FROM `announcements` WHERE `id` = $announcementId")->fetch(PDO::FETCH_ASSOC);
            if ($existingAnnouncement && !empty($existingAnnouncement['attachment'])) {
                $attachmentPath = $uploadDirectory . $existingAnnouncement['attachment'];
                if (file_exists($attachmentPath)) {
                    unlink($attachmentPath); // Delete the file
                }
            }

            // Delete the announcement from the database
            $deleteSql = "DELETE FROM `announcements` WHERE `id` = ?";
            $stmtDelete = $connection->prepare($deleteSql);

            if ($stmtDelete->execute([$announcementId])) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Announcement deleted successfully!'
                    }).then(() => {
                        window.location.href = 'view_events.php';
                    });
                </script>";
            } else {
                echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to delete the announcement. Please try again.'});</script>";
            }
            exit;
        }

        $announcementId = $_POST['id'];
        $title = $_POST['announcement_title'];
        $content = $_POST['announcement_content'];
        $category = $_POST['announcement_category'];
        $audience = $_POST['announcement_audience'];
        $status = $_POST['announcement_status'];

        $file = isset($_FILES['image']) ? $_FILES['image'] : null;
        $attachment = null;

        // Check if a file was uploaded and validate it
        if ($file && $file['error'] === UPLOAD_ERR_OK) {

            $allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 
                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];

            $fileType = mime_content_type($file['tmp_name']);

            if (in_array($fileType, $allowedTypes)) {
                $fileName = uniqid('attachment_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                $filePath = $uploadDirectory . $fileName;

                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $attachment = $fileName; // Set the uploaded file name
                } else {
                    echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to upload the file. Please try again.'});</script>";
                    exit;
                }
            } else {
                echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Invalid file type. Only JPEG, PNG, WEBP, GIF, and supported document formats are allowed.'});</script>";
                exit;
            }
        } else {
            // Keep the existing attachment if no new file is uploaded
            $existingAnnouncement = $connection->query("SELECT `attachment` FROM `announcements` WHERE `id` = $announcementId")->fetch(PDO::FETCH_ASSOC);
            $attachment = $existingAnnouncement['attachment'];
        }

        // Prepare the update SQL query
        $updateSql = "UPDATE `announcements` SET `title` = ?, `content` = ?, `category` = ?, `audience` = ?, `status` = ?, `attachment` = ? WHERE `id` = ?";
        $params = [$title, $content, $category, $audience, $status, $attachment, $announcementId];

        $stmtUpdate = $connection->prepare($updateSql);

        if ($stmtUpdate->execute($params)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Announcement updated successfully!'
                }).then(() => {
                    window.location.href = 'view_events.php';
                });
            </script>";
        } else {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update the announcement. Please try again.'});</script>";
        }
        exit;
    }
?>

<div class="container mt-4">
    <?php if (empty($announcements_list)): ?>
        <div class="alert alert-warning text-center" role="alert">
            No data found.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach ($announcements_list as $announcement): ?>
                <div class="col mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="../components/img/undraw_profile.svg" class="card-img-top rounded-top" alt="Card Image" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-primary text-center text-truncate"><?php echo ($announcement['title']); ?></h5>
                            <p class="card-text text-muted" style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($announcement['content']); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo ($announcement['category']); ?></p>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#view_modal_<?php echo $announcement['id']; ?>">View More</button>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit_modal_<?php echo $announcement['id']; ?>">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delete_modal_<?php echo $announcement['id']; ?>">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="delete_modal_<?php echo $announcement['id']; ?>" tabindex="-1" aria-labelledby="delete_modalLabel_<?php echo $announcement['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="delete_modalLabel_<?php echo $announcement['id']; ?>">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <p>Are you sure you want to delete the announcement titled <strong><?php echo ($announcement['title']); ?></strong>? This action cannot be undone.</p>
                            </div>

                            <div class="modal-footer">
                                <form method="POST" action="">
                                    <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                    <input type="hidden" name="delete_announcement" value="1">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Modal -->
                <div class="modal fade" id="view_modal_<?php echo $announcement['id']; ?>" tabindex="-1" aria-labelledby="view_modalLabel_<?php echo $announcement['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="view_modalLabel_<?php echo $announcement['id']; ?>"><?php echo ($announcement['title']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <img src="../components/img/undraw_profile.svg" class="img-fluid rounded mb-3" alt="Announcement Image">
                                        <?php if (!empty($announcement['attachment'])): ?>
                                            <a href="../uploads/<?php echo ($announcement['attachment']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">View Attachment</a>
                                        <?php else: ?>
                                            <p class="text-muted">No Attachment</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-8">
                                        <p><strong>Title:</strong> <?php echo ($announcement['title']); ?></p>
                                        <p><strong>Content:</strong> <?php echo nl2br($announcement['content']); ?></p>
                                        <p><strong>Category:</strong> <?php echo ($announcement['category']); ?></p>
                                        <p><strong>Posted By:</strong> <?php echo ($announcement['posted_by']); ?></p>
                                        <p><strong>Audience:</strong> <?php echo ($announcement['audience']); ?></p>
                                        <p><strong>Status:</strong>
                                            <span class="badge bg-<?php echo $announcement['status'] === 'Active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ($announcement['status']); ?>
                                            </span>
                                        </p>
                                        <p><strong>Created At:</strong> <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></p>
                                        <p><strong>Updated At:</strong> <?php echo date('F j, Y, g:i a', strtotime($announcement['updated_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="edit_modal_<?php echo $announcement['id']; ?>" tabindex="-1" aria-labelledby="edit_modalLabel_<?php echo $announcement['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="edit_modalLabel_<?php echo $announcement['id']; ?>">Edit Announcement: <?php echo ($announcement['title']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="announcement_title_<?php echo $announcement['id']; ?>" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="announcement_title_<?php echo $announcement['id']; ?>" name="announcement_title" value="<?php echo ($announcement['title']); ?>" placeholder="Enter title" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="announcement_category_<?php echo $announcement['id']; ?>" class="form-label">Category</label>
                                                <select class="form-control" name="announcement_category" id="announcement_category_<?php echo $announcement['id']; ?>" required>
                                                    <option value="" disabled>Select category</option>
                                                    <option value="Events" <?php echo ($announcement['category'] === 'Events' ? 'selected' : ''); ?>>Events</option>
                                                    <option value="Emergency" <?php echo ($announcement['category'] === 'Emergency' ? 'selected' : ''); ?>>Emergency</option>
                                                    <option value="Health" <?php echo ($announcement['category'] === 'Health' ? 'selected' : ''); ?>>Health</option>
                                                    <option value="Public Notice" <?php echo ($announcement['category'] === 'Public Notice' ? 'selected' : ''); ?>>Public Notice</option>
                                                    <option value="Lost & Found" <?php echo ($announcement['category'] === 'Lost & Found' ? 'selected' : ''); ?>>Lost & Found</option>
                                                    <option value="Job Postings" <?php echo ($announcement['category'] === 'Job Postings' ? 'selected' : ''); ?>>Job Postings</option>
                                                    <option value="Community Projects" <?php echo ($announcement['category'] === 'Community Projects' ? 'selected' : ''); ?>>Community Projects</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="announcement_content_<?php echo $announcement['id']; ?>" class="form-label">Content</label>
                                        <textarea class="form-control" id="announcement_content_<?php echo $announcement['id']; ?>" name="announcement_content" rows="4" placeholder="Enter content" required><?php echo ($announcement['content']); ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="announcement_audience_<?php echo $announcement['id']; ?>" class="form-label">Audience</label>
                                                <select class="form-control" name="announcement_audience" id="announcement_audience_<?php echo $announcement['id']; ?>" required>
                                                    <option value="" disabled>Select audience</option>
                                                    <option value="All" <?php echo ($announcement['audience'] === 'All' ? 'selected' : ''); ?>>All</option>
                                                    <option value="Residents" <?php echo ($announcement['audience'] === 'Residents' ? 'selected' : ''); ?>>Residents</option>
                                                    <option value="Officials" <?php echo ($announcement['audience'] === 'Officials' ? 'selected' : ''); ?>>Officials</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="announcement_status_<?php echo $announcement['id']; ?>" class="form-label">Status</label>
                                                <select class="form-control" name="announcement_status" id="announcement_status_<?php echo $announcement['id']; ?>" required>
                                                    <option value="" disabled>Select status</option>
                                                    <option value="Active" <?php echo ($announcement['status'] === 'Active' ? 'selected' : ''); ?>>Active</option>
                                                    <option value="Archived" <?php echo ($announcement['status'] === 'Archived' ? 'selected' : ''); ?>>Archived</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image_<?php echo $announcement['id']; ?>" class="form-label">Upload Attachment</label>
                                        <input type="file" class="form-control" name="image" id="image_<?php echo $announcement['id']; ?>" accept="image/*">
                                        <small class="form-text text-muted">Optional: Upload an image or document.</small>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>