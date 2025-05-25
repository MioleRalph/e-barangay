<?php
    include '../includes/resident/resident_sidebar.php';
    $announcements_list = $connection->query("SELECT * FROM `announcements` WHERE `category` = 'Health'")->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

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

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>