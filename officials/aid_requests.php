<?php 

    include '../includes/official/official_sidebar.php'; 

    
    $aid_request_list = $connection->query("SELECT * FROM `aid_requests` ORDER BY `date_requested` DESC")->fetchAll(PDO::FETCH_ASSOC);

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


    // Approve Aid Request
    if (isset($_POST['approve_aid_request'])) {
        $approve_id = $_POST['approve_id'];

        $verify_approve = $connection->prepare("SELECT * FROM `aid_requests` WHERE id = ?");
        $verify_approve->execute([$approve_id]);

        if ($verify_approve->rowCount() > 0) {
            $aid_request = $verify_approve->fetch(PDO::FETCH_ASSOC);

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

            $update_status = $connection->prepare("UPDATE `aid_requests` SET `status` = 'approved', `date_approved` = NOW() WHERE id = ?");
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

                // Log the approval
                $log_activity = $connection->prepare("INSERT INTO `aid_requests_logs` (`approved_id`, `beneficiary_id`, `beneficiary_name`, `approved_by`, `activity`, `timestamp`) VALUES (?, ?, ?, ?, ?, NOW())");
                $log_activity->execute([$approved_id, $aid_request['beneficiary_id'], $aid_request['beneficiary_name'], $_SESSION['full_name'], 'Approved aid request ID: ' . $approve_id]);

                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Aid request has been approved successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'aid_requests.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error approving aid request.',
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
                    text: 'Aid request not found!',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>";
        }
    }

    // Reject Aid Request
    if (isset($_POST['reject_aid_request'])) {
        $reject_id = $_POST['reject_id'];

        $verify_reject = $connection->prepare("SELECT * FROM `aid_requests` WHERE id = ?");
        $verify_reject->execute([$reject_id]);

        if ($verify_reject->rowCount() > 0) {
            $aid_request = $verify_reject->fetch(PDO::FETCH_ASSOC);

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

            $update_status = $connection->prepare("UPDATE `aid_requests` SET `status` = 'rejected', `date_approved` = NOW() WHERE id = ?");
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

                // Log the approval
                $log_activity = $connection->prepare("INSERT INTO `aid_requests_logs` (`approved_id`, `beneficiary_id`, `beneficiary_name`, `approved_by`, `activity`, `timestamp`) VALUES (?, ?, ?, ?, ?, NOW())");
                $log_activity->execute([$reject_id, $aid_request['beneficiary_id'], $aid_request['beneficiary_name'], $_SESSION['full_name'], 'Rejected aid request of: ' . $aid_request['beneficiary_name']]);

                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Rejected!',
                            text: 'Aid request has been rejected successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'aid_requests.php';
                        });
                    </script>";
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error rejecting aid request.',
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
                        text: 'Aid request not found!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                </script>";
        }
    }
?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Aid Requests</h1>

<p class="mb-4">
    Below is the list of all aid requests submitted by beneficiaries. You can approve, reject, or delete requests as needed. Use the "Download" button to export the data.
</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Aid Requests List</h6>
        <a href="../xml/export_aid_request.php" class="btn btn-primary btn-sm">
            <i class="fas fa-file-export"></i> Download
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Beneficiary ID</th>
                        <th>Beneficiary Name</th>
                        <th>Aid Type</th>
                        <th>Request Reason</th>
                        <th>Status</th>
                        <th>Amount Requested</th>
                        <th>Date Requested</th>
                        <th>Date Approved</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Beneficiary ID</th>
                        <th>Beneficiary Name</th>
                        <th>Aid Type</th>
                        <th>Request Reason</th>
                        <th>Status</th>
                        <th>Amount Requested</th>
                        <th>Date Requested</th>
                        <th>Date Approved</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($aid_request_list as $aid_request):
                        // Status badge color
                        $status = strtolower($aid_request['status']);
                        $badgeClass = 'secondary';
                        if ($status == 'approved') $badgeClass = 'success';
                        elseif ($status == 'rejected') $badgeClass = 'danger';
                        elseif ($status == 'pending') $badgeClass = 'warning';
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo ($aid_request['beneficiary_id']); ?></td>
                            <td><?php echo ($aid_request['beneficiary_name']); ?></td>
                            <td><?php echo ($aid_request['aid_type']); ?></td>
                            <td><?php echo ($aid_request['request_reason']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $badgeClass; ?> text-uppercase">
                                    <?php echo ucfirst($aid_request['status']); ?>
                                </span>
                            </td>
                            <td><?php echo ($aid_request['amount_requested']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($aid_request['date_requested'])); ?></td>
                            <td><?php echo ($aid_request['date_approved'] ? date('F j, Y g:i A', strtotime($aid_request['date_approved'])) : 'N/A'); ?></td>
                        

                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="approve_id" value="<?php echo $aid_request['id']; ?>">
                                        <input type="hidden" name="approve_aid_request" value="1">
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="reject_id" value="<?php echo $aid_request['id']; ?>">
                                        <input type="hidden" name="reject_aid_request" value="1">
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
