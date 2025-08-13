<?php
include '../includes/admin/admin_sidebar.php';


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

    $verify_delete = $connection->prepare("SELECT * FROM `aid_requests` WHERE id = ?");
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

        $delete_aid_request = $connection->prepare("DELETE FROM `aid_requests` WHERE id = ?");
        if ($delete_aid_request->execute([$delete_id])) {
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

            // Log the deletion
            $log_activity = $connection->prepare("INSERT INTO `aid_requests_logs` (`approved_id`, `beneficiary_id`, `beneficiary_name`, `approved_by`, `activity`, `timestamp`) VALUES (?, ?, ?, ?, ?, NOW())");
            $log_activity->execute([$approved_id, $aid_request['beneficiary_id'], $aid_request['beneficiary_name'], $_SESSION['full_name'], 'Deleted aid request ID: ' . $delete_id]);

            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Aid request has been deleted successfully.',
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
            $log_activity->execute([$approved_id, $aid_request['beneficiary_id'], $aid_request['beneficiary_name'], $_SESSION['full_name'], 'Approved aid request of: ' . $aid_request['beneficiary_name']]);

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
            <i class="fas fa-file-export"></i> Download Excel
        </a>

        <a href="../pdf/aid_requests_report.php" class="btn btn-primary btn-sm">
            <i class="fas fa-file-export"></i> Download PDF
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Resident ID</th>
                        <th>Resident Name</th>
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
                        <th>Resident ID</th>
                        <th>Resident Name</th>
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
                            <td><?php echo htmlspecialchars($aid_request['beneficiary_id']); ?></td>
                            <td><?php echo htmlspecialchars($aid_request['beneficiary_name']); ?></td>
                            <td><?php echo htmlspecialchars($aid_request['aid_type']); ?></td>
                            <td><?php echo htmlspecialchars($aid_request['request_reason']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $badgeClass; ?> text-uppercase">
                                    <?php echo ucfirst($aid_request['status']); ?>
                                </span>
                            </td>
                            <td>₱<?php echo number_format($aid_request['amount_requested'], 2); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($aid_request['date_requested'])); ?></td>
                            <td><?php echo ($aid_request['date_approved'] ? date('F j, Y g:i A', strtotime($aid_request['date_approved'])) : '<span class="text-muted">N/A</span>'); ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <form method="POST" action="" class="delete-form d-inline">
                                        <input type="hidden" name="delete_id" value="<?php echo $aid_request['id']; ?>">
                                        <input type="hidden" name="delete_aid_request" value="1">
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
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