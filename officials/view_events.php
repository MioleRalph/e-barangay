<?php
    include '../includes/official/official_sidebar.php';
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    $uploadDirectory = __DIR__ . '/../uploads/'; // Absolute path to 'uploads' directory

    if (!is_writable($uploadDirectory)) {
        echo "<script>Swal.fire('Error', 'Directory is not writable: $uploadDirectory', 'error');</script>";
        exit;
    }

    // Fetch announcements safely
    $stmt = $connection->prepare("SELECT * FROM `announcements` WHERE `category` = 'Events' ORDER BY `created_at` DESC");
    $stmt->execute();
    $announcements_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decrypt title and content safely
    foreach ($announcements_list as $key => $announcement) {
        $announcements_list[$key]['title'] = decryptData($announcement['title']);
        $announcements_list[$key]['content'] = decryptData($announcement['content']);
    }

    // Handle POST actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // DELETE
        if (isset($_POST['delete_announcement']) && $_POST['delete_announcement'] == 1) {
            $announcementId = $_POST['announcement_id'];
            $stmt = $connection->prepare("SELECT `attachment` FROM `announcements` WHERE `id` = ?");
            $stmt->execute([$announcementId]);
            $existingAnnouncement = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingAnnouncement && !empty($existingAnnouncement['attachment'])) {
                $attachmentPath = $uploadDirectory . $existingAnnouncement['attachment'];
                if (file_exists($attachmentPath)) unlink($attachmentPath);
            }

            $stmtDelete = $connection->prepare("DELETE FROM `announcements` WHERE `id` = ?");
            if ($stmtDelete->execute([$announcementId])) {
                echo "<script>
                    Swal.fire({icon: 'success', title: 'Deleted', text: 'Announcement deleted successfully!'})
                    .then(() => { window.location.href = 'view_events.php'; });
                </script>";
            } else {
                echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to delete the announcement.'});</script>";
            }
            exit;
        }

        // UPDATE
        $announcementId = $_POST['id'];
        $title = $_POST['announcement_title'];
        $content = $_POST['announcement_content'];
        $category = $_POST['announcement_category'];
        $audience = $_POST['announcement_audience'];
        $status = $_POST['announcement_status'];

        $encryptedTitle = encryptData($title);
        $encryptedContent = encryptData($content);

        // Handle attachment
        $file = $_FILES['image'] ?? null;
        $attachment = null;

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];

            $fileType = mime_content_type($file['tmp_name']);
            if (in_array($fileType, $allowedTypes)) {
                $fileName = uniqid('attachment_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                $filePath = $uploadDirectory . $fileName;
                if (move_uploaded_file($file['tmp_name'], $filePath)) $attachment = $fileName;
                else {
                    echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to upload the file.'});</script>";
                    exit;
                }
            } else {
                echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Invalid file type.'});</script>";
                exit;
            }
        } else {
            $stmt = $connection->prepare("SELECT `attachment` FROM `announcements` WHERE `id` = ?");
            $stmt->execute([$announcementId]);
            $existingAnnouncement = $stmt->fetch(PDO::FETCH_ASSOC);
            $attachment = $existingAnnouncement['attachment'] ?? null;
        }

        $stmtUpdate = $connection->prepare("UPDATE `announcements` SET `title` = ?, `content` = ?, `category` = ?, `audience` = ?, `status` = ?, `attachment` = ? WHERE `id` = ?");
        if ($stmtUpdate->execute([$encryptedTitle, $encryptedContent, $category, $audience, $status, $attachment, $announcementId])) {
            echo "<script>
                Swal.fire({icon: 'success', title: 'Success', text: 'Announcement updated successfully!'})
                .then(() => { window.location.href = 'view_events.php'; });
            </script>";
        } else {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update the announcement.'});</script>";
        }
        exit;
    }
?>

<div class="container mt-4">
    <?php if (empty($announcements_list)): ?>
        <div class="alert alert-warning text-center">
            No data found.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach ($announcements_list as $announcement): ?>
                <div class="col mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="../components/img/undraw_profile.svg" class="card-img-top rounded-top" alt="Card Image" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-primary text-center text-truncate"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                            <p class="card-text text-muted" style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($announcement['content']); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($announcement['category']); ?></p>
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

                <!-- VIEW MODAL -->
                <div class="modal fade" id="view_modal_<?php echo $announcement['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                                <?php if (!empty($announcement['attachment'])): ?>
                                    <p><strong>Attachment:</strong> <a href="../uploads/<?php echo $announcement['attachment']; ?>" target="_blank"><?php echo htmlspecialchars($announcement['attachment']); ?></a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="edit_modal_<?php echo $announcement['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Announcement</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                    <div class="mb-3">
                                        <label>Title</label>
                                        <input type="text" name="announcement_title" class="form-control" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Content</label>
                                        <textarea name="announcement_content" class="form-control" rows="5" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Category</label>
                                        <input type="text" name="announcement_category" class="form-control" value="<?php echo htmlspecialchars($announcement['category']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Audience</label>
                                        <input type="text" name="announcement_audience" class="form-control" value="<?php echo htmlspecialchars($announcement['audience']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select name="announcement_status" class="form-select" required>
                                            <option value="Active" <?php echo $announcement['status']=='Active'?'selected':''; ?>>Active</option>
                                            <option value="Inactive" <?php echo $announcement['status']=='Inactive'?'selected':''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label>Attachment</label>
                                        <input type="file" name="image" class="form-control">
                                        <?php if (!empty($announcement['attachment'])): ?>
                                            <small>Current: <?php echo htmlspecialchars($announcement['attachment']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Update</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- DELETE MODAL -->
                <div class="modal fade" id="delete_modal_<?php echo $announcement['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete "<strong><?php echo htmlspecialchars($announcement['title']); ?></strong>"?
                                    <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                    <input type="hidden" name="delete_announcement" value="1">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
