<?php
    include '../includes/official/official_sidebar.php';

    // Use a prepared statement to fetch logs
    $query = $connection->prepare("SELECT * FROM `file_request` WHERE `transaction_type` = ?");
    $query->execute(['Blotter']);
    $blotter_requests = $query->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    // Approve Aid Request
    if (isset($_POST['approve_request'])) {
        $approve_id = $_POST['approve_id'];

        $verify_approve = $connection->prepare("SELECT * FROM `file_request` WHERE id = ?");
        $verify_approve->execute([$approve_id]);

        if ($verify_approve->rowCount() > 0) {
            $aid_request = $verify_approve->fetch(PDO::FETCH_ASSOC);

            if (!isset($aid_request) || empty($aid_request)) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Blotter request data is not set or invalid.'
                    });
                </script>";
                exit();
            }

            $update_status = $connection->prepare("UPDATE `file_request` SET `transaction_status` = 'approved' WHERE id = ?");
            if ($update_status->execute([$approve_id])) {
                // Ensure approved_id is not null
                $approved_id = $user_id;
                if (empty($approved_id)) {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Approved ID cannot be null.'
                        });
                    </script>";
                    exit();
                }

                // Find the correct request from the list using approve_id
                $beneficiary_id = null;
                $beneficiary_name = null;
                foreach ($blotter_requests as $req) {
                    if ($req['id'] == $approve_id) {
                        $beneficiary_id = $req['user_id'];
                        $beneficiary_name = $req['name'];
                        break;
                    }
                }
                if (empty($beneficiary_id) || empty($beneficiary_name)) {
                    echo '<script>Swal.fire({icon: "error", title: "Error", text: "Beneficiary information is missing or invalid."});</script>';
                    exit();
                }
                // Log the approval
                $log_activity = $connection->prepare("INSERT INTO `aid_requests_logs` (`approved_id`, `beneficiary_id`, `beneficiary_name`, `approved_by`, `activity`, `timestamp`) VALUES (?, ?, ?, ?, ?, NOW())");
                $log_activity->execute([$approved_id, $beneficiary_id, $beneficiary_name, $_SESSION['full_name'], 'Request of Blotter Approved']);

                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Blotter request has been approved successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'blotter.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error approving Blotter request.',
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
                    text: 'Blotter request not found!',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>";
        }
    }

    // Reject Aid Request
    if (isset($_POST['reject_request'])) {
        $reject_id = $_POST['reject_id'];

        $verify_reject = $connection->prepare("SELECT * FROM `file_request` WHERE id = ?");
        $verify_reject->execute([$reject_id]);

        if ($verify_reject->rowCount() > 0) {
            $aid_request = $verify_reject->fetch(PDO::FETCH_ASSOC);

            if (!isset($aid_request) || empty($aid_request)) {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Blotter request data is not set or invalid.'
                        });
                    </script>";
                exit();
            }

            $update_status = $connection->prepare("UPDATE `file_request` SET `transaction_status` = 'Rejected' WHERE id = ?");
            if ($update_status->execute([$reject_id])) {
                // Ensure reject_id is not null
                $reject_id = $user_id;
                if (empty($reject_id)) {
                    echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Approved ID cannot be null.'
                            });
                        </script>";
                    exit();
                }

                $beneficiary_id = null;
                $beneficiary_name = null;
                // Use the original POST reject_id to find the beneficiary, not the overwritten $reject_id
                foreach ($blotter_requests as $req) {
                    if ($req['id'] == $_POST['reject_id']) {
                        $beneficiary_id = $req['user_id'];
                        $beneficiary_name = $req['name'];
                        break;
                    }
                }
                if (empty($beneficiary_id) || empty($beneficiary_name)) {
                    echo '<script>Swal.fire({icon: "error", title: "Error", text: "Beneficiary information is missing or invalid."});</script>';
                    exit();
                }
                // Log the rejection
                $log_activity = $connection->prepare("INSERT INTO `aid_requests_logs` (`approved_id`, `beneficiary_id`, `beneficiary_name`, `approved_by`, `activity`, `timestamp`) VALUES (?, ?, ?, ?, ?, NOW())");
                $log_activity->execute([$reject_id,  $beneficiary_id, $beneficiary_name, $_SESSION['full_name'], 'Financial Assistance Rejected']);

                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Rejected!',
                            text: 'Blotter request has been rejected successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'blotter.php';
                        });
                    </script>";
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error rejecting Blotter request.',
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
                        text: 'Blotter request not found!',
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
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users"></i> Activity Logs
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Date Of Birth</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Transaction Status</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                    <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Date Of Birth</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Transaction Status</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($blotter_requests as $logs):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo ($logs['name']); ?></td>
                            <td><?php echo ($logs['user_id']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($logs['date_of_birth'])); ?></td>
                            <td><?php echo ($logs['email']); ?></td>
                            <td><?php echo ($logs['address']); ?></td>
                            <td><?php echo ($logs['amount']); ?></td>
                            <td><?php echo ($logs['transaction_type']); ?></td>
                            <td><?php echo ($logs['transaction_status']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($logs['date_submitted'])); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="approve_id" value="<?php echo $logs['id']; ?>">
                                        <input type="hidden" name="approve_request" value="1">
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="reject_id" value="<?php echo $logs['id']; ?>">
                                        <input type="hidden" name="reject_request" value="1">
                                        <button type="submit" class="btn btn-warning btn-sm" title="Reject">
                                            <i class="fas fa-times"></i>
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
