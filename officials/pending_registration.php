<?php
    include '../includes/official/official_sidebar.php';

    // Use a prepared statement to fetch pending accounts
    $query = $connection->prepare("
        SELECT `account_id`, `first_name`, `last_name`, `date_of_birth`, `email`, `date_registered`, `approval_status`
        FROM `accounts`
        WHERE `approval_status` IN (?,?,?)
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

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    // Approve account
    if (isset($_POST['approve_request'])) {
        $approve_id = $_POST['approve_id'];

        // Validate input
        if (empty($approve_id)) {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Invalid account selected.'});</script>";
            exit();
        }

        // Verify account exists
        $verify = $connection->prepare("SELECT * FROM `accounts` WHERE `account_id` = ?");
        $verify->execute([$approve_id]);

        if ($verify->rowCount() === 0) {
            echo "<script>Swal.fire({icon: 'warning', title: 'Not found', text: 'Account not found.'});</script>";
            exit();
        }

        // Update approval_status to approved
        $update = $connection->prepare("UPDATE `accounts` SET `approval_status` = 'approved' WHERE `account_id` = ?");
        if ($update->execute([$approve_id])) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Approved',
                    text: 'Account has been approved.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => { window.location.href = 'pending_registration.php'; });
            </script>";
            exit();
        } else {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to approve account.'});</script>";
            exit();
        }
    }

    // Reject account
    if (isset($_POST['reject_request'])) {
        $reject_id = $_POST['reject_id'];

        // Validate input
        if (empty($reject_id)) {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Invalid account selected.'});</script>";
            exit();
        }

        // Verify account exists
        $verify = $connection->prepare("SELECT * FROM `accounts` WHERE `account_id` = ?");
        $verify->execute([$reject_id]);

        if ($verify->rowCount() === 0) {
            echo "<script>Swal.fire({icon: 'warning', title: 'Not found', text: 'Account not found.'});</script>";
            exit();
        }

        // Update approval_status to rejected
        $update = $connection->prepare("UPDATE `accounts` SET `approval_status` = 'rejected' WHERE `account_id` = ?");
        if ($update->execute([$reject_id])) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Rejected',
                    text: 'Account has been rejected.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => { window.location.href = 'pending_registration.php'; });
            </script>";
            exit();
        } else {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to reject account.'});</script>";
            exit();
        }
    }

    // Delete account
    if (isset($_POST['delete_request'])) {
        $delete_id = $_POST['delete_id'];

        // Validate input
        if (empty($delete_id)) {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Invalid account selected.'});</script>";
            exit();
        }

        // Verify account exists
        $verify = $connection->prepare("SELECT * FROM `accounts` WHERE `account_id` = ?");
        $verify->execute([$delete_id]);

        if ($verify->rowCount() === 0) {
            echo "<script>Swal.fire({icon: 'warning', title: 'Not found', text: 'Account not found.'});</script>";
            exit();
        }

        // Delete account
        $delete = $connection->prepare("DELETE FROM `accounts` WHERE `account_id` = ?");
        if ($delete->execute([$delete_id])) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Account has been deleted.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => { window.location.href = 'pending_registration.php'; });
            </script>";
            exit();
        } else {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Failed to delete account.'});</script>";
            exit();
        }
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
                            <td><?php echo ($pending['first_name'] . ' ' . $pending['last_name']); ?></td>
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
