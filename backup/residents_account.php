<?php 
    include '../includes/admin/admin_sidebar.php'; 

    $residents_account = $connection->query("
        SELECT accounts.*, roles.role_name 
        FROM `accounts` 
        LEFT JOIN `roles` ON accounts.user_type = roles.id 
        WHERE `user_type` = '2'
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
                        window.location.href = 'residents_account.php';
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

<style>
    .card-box {
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        background-color: #f8f9fa;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    }

    .thumb-lg {
        height: 100px;
        width: 100px;
    }

    .img-thumbnail {
        padding: .25rem;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 50%;
        max-width: 100%;
        height: auto;
    }

    .text-pink {
        color: #e83e8c !important;
    }

    .btn-rounded {
        border-radius: 50px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .text-muted {
        color: #6c757d !important;
    }

    h4 {
        line-height: 24px;
        font-size: 20px;
        font-weight: 600;
        color: #343a40;
    }

    p.text-muted {
        font-size: 14px;
    }
</style>

<div class="row">
    <?php if (empty($residents_account)): ?>
        <div class="col-12 text-center">
            <h4 class="text-danger">No accounts found.</h4>
        </div>
    <?php else: ?>
        <?php foreach ($residents_account as $account): ?>
            <div class="col-lg-4">
                <div class="text-center card-box">
                    <div class="member-card pt-2 pb-2">
                        <div class="thumb-lg member-thumb mx-auto">
                            <img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="rounded-circle img-thumbnail" alt="profile-image">
                        </div>
                        <div class="">
                            <h4><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></h4>
                            <p class="text-muted">@<?php echo ($account['role_name']); ?> <span>| </span><span><a href="#" class="text-pink"><?php echo htmlspecialchars($account['email']); ?></a></span></p>
                        </div>

                        <!-- to remove the account to the database (permanent deletion) -->
                        <form method="POST" action="" class="d-inline">
                            <input type="hidden" name="account_id" value="<?php echo $account['account_id']; ?>">
                            <input type="hidden" name="delete_account" value="1">
                            <button type="submit" class="btn btn-danger btn-rounded waves-effect w-md waves-light">Remove Account</button>
                        </form>

                        <!-- to view the details of the account -->
                        <a href="resident_account_details.php?account_id=<?php echo $account['account_id']; ?>" class="btn btn-primary btn-rounded waves-effect w-md waves-light">View Details</a>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- end col -->
</div>
<!-- end row -->

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