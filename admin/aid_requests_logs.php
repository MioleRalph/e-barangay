<?php
    include '../includes/admin/admin_sidebar.php';


    $aid_request_logs = $connection->query("SELECT * FROM `aid_requests_logs` ORDER BY `timestamp` DESC")->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    // Ensure user_id is set
    if (empty($user_id)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Session Error',
                text: 'User ID is not set. Please log in again.'
            }).then(() => {
                window.location.href = '../login.php';
            });
        </script>";
        exit();
    }

    // Delete Aid Request
    if (isset($_POST['delete_aid_request'])) {
        $delete_id = $_POST['delete_id'];

        // Validate $delete_id and $aid_request before use
        if (!isset($delete_id) || empty($delete_id)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Delete ID is not set or invalid.'
                });
            </script>";
            exit();
        }

        $verify_delete = $connection->prepare("SELECT * FROM `aid_requests_logs` WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $aid_request = $verify_delete->fetch(PDO::FETCH_ASSOC);

            if (!isset($aid_request) || empty($aid_request)) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Aid request data is not set or invalid.'
                    });
                </script>";
                exit();
            }

            $delete_aid_request = $connection->prepare("DELETE FROM `aid_requests_logs` WHERE id = ?");
            if ($delete_aid_request->execute([$delete_id])) {

                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Aid request has been deleted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'aid_requests_logs.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error deleting aid request.',
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
                    text: 'Aid request already deleted!',
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
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Approved ID</th>
                        <th>Resident ID</th>
                        <th>Resident Name</th>
                        <th>Approved By</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Approved ID</th>
                        <th>Resident ID</th>
                        <th>Resident Name</th>
                        <th>Approved By</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($aid_request_logs as $aid_logs):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo ($aid_logs['approved_id']); ?></td>
                            <td><?php echo ($aid_logs['beneficiary_id']); ?></td>
                            <td><?php echo ($aid_logs['beneficiary_name']); ?></td>
                            <td><?php echo ($aid_logs['approved_by']); ?></td>
                            <td><?php echo ($aid_logs['activity']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($aid_logs['timestamp'])); ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <form method="POST" action="" class="delete-form d-inline">
                                        <input type="hidden" name="delete_id" value="<?php echo $aid_logs['id']; ?>">
                                        <input type="hidden" name="delete_aid_request" value="1">
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
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