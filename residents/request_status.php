<?php
    include '../includes/resident/resident_sidebar.php';

    $query = $connection->prepare("SELECT * FROM `file_request` WHERE `user_id` = ? ORDER BY `date_submitted` DESC");
    $query->execute([$user_id]);
    $request_status = $query->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Request Status</h1>
<p class="mb-4">
    Below is a summary of your recent file requests, including their status and details. Please review your activity and track the progress of each transaction.
</p>

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
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($request_status as $status):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo (decryptData($status['name'])); ?></td>
                            <td><?php echo ($status['date_of_birth']); ?></td>
                            <td><?php echo ($status['email']); ?></td>
                            <td><?php echo (decryptData($status['amount'])); ?></td>
                            <td><?php echo ($status['transaction_type']); ?></td>
                            <td>
                                <?php
                                    $activity = htmlspecialchars($status['transaction_status']);
                                    $badgeClass = 'badge-info';

                                    $statusValue = strtolower($status['transaction_status']); // normalize

                                    if ($statusValue === 'approved') {
                                        $badgeClass = 'badge-success';
                                    } elseif ($statusValue === 'rejected') {
                                        $badgeClass = 'badge-danger';
                                    } elseif ($statusValue === 'pending') {
                                        $badgeClass = 'badge-info';
                                    }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $activity; ?>
                                </span>
                            </td>
                            <td><?php echo date('F j, Y g:i A', strtotime($status['date_submitted'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
