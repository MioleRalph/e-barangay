<?php 
    include '../includes/admin/admin_sidebar.php'; 

    $announcement_list = $connection->query("SELECT * FROM `announcements` ORDER BY `created_at` DESC")->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    // Delete announcement logic
    if (isset($_POST['delete_announcement'])) {
        $delete_id = $_POST['announcement_id'];
    
        $verify_delete = $connection->prepare("SELECT * FROM `announcements` WHERE id = ?");
        $verify_delete->execute([$delete_id]);
    
        if ($verify_delete->rowCount() > 0) {
            $delete_announcement = $connection->prepare("DELETE FROM `announcements` WHERE id = ?");
            if ($delete_announcement->execute([$delete_id])) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Announcement has been deleted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'announcements.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error deleting announcement.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Announcement already deleted!',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>";
        }
    }
?>

<div class="row">
    <?php if (empty($announcement_list)): ?>
        <div class="col-12 text-center">
            <h4 class="text-danger">No announcements found.</h4>
        </div>
    <?php else: ?>
        <div class="container mt-4">
            
            <h2 class="text-center mb-4">Announcements</h2>
            <div class="d-flex justify-content-end mb-4">
                <a href="../xml/exportAnnouncements.php" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Export Announcements XML
                </a>

                <a href="../pdf/aid_event_report.php" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> PDF Download
                </a>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($announcement_list as $announcement): ?>
                    <div class="col mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($announcement['attachment'])): ?>
                                <img src="../uploads/<?php echo ($announcement['attachment']); ?>" class="card-img-top rounded-top" alt="Attachment" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="card-img-top rounded-top" alt="No Attachment" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title text-center text-primary"><?php echo ($announcement['title']); ?></h5>
                                <p class="card-text"><?php echo nl2br(($announcement['content'])); ?></p>
                                <p class="card-text"><strong>Category:</strong> <?php echo ($announcement['category']); ?></p>
                                <p class="card-text"><strong>Audience:</strong> <?php echo ($announcement['audience']); ?></p>
                                <p class="card-text"><strong>Status:</strong> <?php echo ($announcement['status']); ?></p>
                                <p class="card-text"><small class="text-muted">Posted by: <?php echo ($announcement['posted_by']); ?> | <?php echo date('F g:i A', strtotime($announcement["created_at"])); ?></small></p>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#view_modal_<?php echo $announcement['id']; ?>">View Details</button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delete_modal_<?php echo $announcement['id']; ?>">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- View Modal -->
                    <div class="modal fade" id="view_modal_<?php echo $announcement['id']; ?>" tabindex="-1" aria-labelledby="view_modalLabel_<?php echo $announcement['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="view_modalLabel_<?php echo $announcement['id']; ?>">Announcement Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h4><?php echo ($announcement['title']); ?></h4>
                                    <p><?php echo nl2br(($announcement['content'])); ?></p>
                                    <p><strong>Category:</strong> <?php echo ($announcement['category']); ?></p>
                                    <p><strong>Audience:</strong> <?php echo ($announcement['audience']); ?></p>
                                    <p><strong>Status:</strong> <?php echo ($announcement['status']); ?></p>
                                    <p><strong>Posted by:</strong> <?php echo ($announcement['posted_by']); ?></p>
                                    <p><strong>Created at:</strong> <?php echo date('F g:i A', strtotime($announcement["created_at"])); ?></p>
                                    <p><strong>Updated at:</strong> <?php echo date('F g:i A', strtotime($announcement["updated_at"])); ?></p>
                                    <?php if (!empty($announcement['attachment'])): ?>
                                        <p><strong>Attachment:</strong> <a href="../uploads/<?php echo ($announcement['attachment']); ?>" target="_blank">View</a></p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                                    <p>Are you sure you want to delete the announcement <strong><?php echo ($announcement['title']); ?></strong>? This action cannot be undone.</p>
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
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
