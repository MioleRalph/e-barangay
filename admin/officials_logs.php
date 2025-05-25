<?php
include '../includes/admin/admin_sidebar.php';

$official_logs = $connection->query("SELECT * FROM `logs` WHERE `user_type` = '3' ORDER BY `timestamp` DESC")->fetchAll(PDO::FETCH_ASSOC);

echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';


if (isset($_POST['delete_logs'])) {
    $delete_id = $_POST['delete_id'];

    $verify_delete = $connection->prepare("SELECT * FROM `logs` WHERE log_id = ?");
    $verify_delete->execute([$delete_id]);

    if ($verify_delete->rowCount() > 0) {
        $delete_logs = $connection->prepare("DELETE FROM `logs` WHERE log_id = ?");
        if ($delete_logs->execute([$delete_id])) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Log has been deleted successfully.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'officials_logs.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error deleting log.',
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
                text: 'Log already deleted!',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}

?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Tables</h1>
<p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
    For more information about DataTables, please visit the <a target="_blank"
        href="https://datatables.net">official DataTables documentation</a>.</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Officials Log</h6>
        <a href="../xml/export_official_logs.php" class="btn btn-primary btn-sm">
            <i class="fas fa-file-export"></i> Download
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Email</th>
                        <th>Activity</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Email</th>
                        <th>Activity</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($official_logs as $logs):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo ($logs['name']); ?></td>
                            <td><?php echo ($logs['user_id']); ?></td>
                            <td><?php echo ($logs['email']); ?></td>
                            <td>
                                <?php
                                $activity = ($logs['activity_type']);
                                $badgeClass = 'badge-info';
                                if (strtolower($logs['activity_type']) === 'login') {
                                    $badgeClass = 'badge-success';
                                } elseif (strtolower($logs['activity_type']) === 'logout') {
                                    $badgeClass = 'badge-danger';
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $activity; ?>
                                </span>
                            </td>
                            <td><?php echo date('F j, Y g:i A', strtotime($logs['timestamp'])); ?></td>
                            <td class="text-center">
                                <form method="POST" action="" class="delete-form d-inline">
                                    <input type="hidden" name="delete_id" value="<?php echo ($logs['log_id']); ?>">
                                    <input type="hidden" name="delete_logs" value="1">
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
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