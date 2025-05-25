<?php 
    include '../includes/admin/admin_sidebar.php'; 

    $officials_account = $connection->query("
                            SELECT accounts.*, roles.role_name 
                            FROM `accounts` 
                            LEFT JOIN `roles` ON accounts.user_type = roles.id 
                            WHERE `user_type` = '3'
                        ")->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    if (isset($_POST['delete_account'])) {
        $delete_id = $_POST['account_id'];
    
        $verify_delete = $connection->prepare("SELECT * FROM `accounts` WHERE account_id = ?");
        $verify_delete->execute([$delete_id]);
    
        if ($verify_delete->rowCount() > 0) {
            $delete_account = $connection->prepare("DELETE FROM `accounts` WHERE account_id = ?");
            if ($delete_account->execute([$delete_id])) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Account has been deleted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'officials_account.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error deleting account.',
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
                    text: 'Account already deleted!',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>";
        }
    }

?>

<div class="row">
    <?php if (empty($officials_account)): ?>
        <div class="col-12 text-center">
            <h4 class="text-danger">No accounts found.</h4>
        </div>
    <?php else: ?>
        <div class="container mt-4">

            <div class="d-flex justify-content-end mb-4">
                <a href="../xml/exportOfficials.php" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Export Officials
                </a>
            </div>


            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($officials_account as $account): ?>
                    <div class="col mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="card-img-top rounded-top" alt="Profile Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title text-primary"><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></h5>
                                <p class="card-text text-muted">@<?php echo ($account['role_name']); ?></p>
                                <p class="card-text"><strong>Email:</strong> <?php echo ($account['email']); ?></p>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#view_modal_<?php echo $account['account_id']; ?>">View Details</button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delete_modal_<?php echo $account['account_id']; ?>">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- View Modal -->
                    <div class="modal fade" id="view_modal_<?php echo $account['account_id']; ?>" tabindex="-1" aria-labelledby="view_modalLabel_<?php echo $account['account_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="view_modalLabel_<?php echo $account['account_id']; ?>">Account Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="img-fluid rounded mb-3" alt="Profile Image">
                                        </div>
                                        <div class="col-md-8">
                                            <p><strong>Name:</strong> <?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($account['email']); ?></p>
                                            <p><strong>Role:</strong> <?php echo ($account['role_name']); ?></p>
                                            <p><strong>Account ID:</strong> <?php echo ($account['account_id']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="delete_modal_<?php echo $account['account_id']; ?>" tabindex="-1" aria-labelledby="delete_modalLabel_<?php echo $account['account_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="delete_modalLabel_<?php echo $account['account_id']; ?>">Confirm Deletion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <div class="modal-body">
                                    <p>Are you sure you want to delete the account of <strong><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></strong>? This action cannot be undone.</p>
                                </div>

                                <div class="modal-footer">
                                    <form method="POST" action="">
                                        <input type="hidden" name="account_id" value="<?php echo $account['account_id']; ?>">
                                        <input type="hidden" name="delete_account" value="1">
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

<!-- DELETE SWEETALERT2 -->
<script>
    // Delete confirmation
        $('.delete-btn').on('click', function() {
            const form = $(this).closest('.delete-form');
            const reviewId = form.find('input[name="delete_id"]').val();

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log("Deleting log ID: " + reviewId); // Debug log
                    form.submit(); // Submit the form if confirmed
                }
            });
        });
</script>