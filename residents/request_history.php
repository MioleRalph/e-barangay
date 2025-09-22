<?php
    include '../includes/resident/resident_sidebar.php';

    $query = $connection->prepare("SELECT * FROM `resident_request_logs` WHERE `account_id` = ? ORDER BY `timestamp` DESC");
    $query->execute([$user_id]);
    $resident_logs = $query->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">My Request Activity Logs</h1>
<p class="mb-4">
    Below is a detailed history of your recent requests and activities. Use the table to review your submissions, track statuses, and verify timestamps for each action performed.
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
                        <th>Activity</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Activity</th>
                        <th>Timestamp</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($resident_logs as $logs):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo ($logs['name']); ?></td>
                            <td><?php echo ($logs['account_id']); ?></td>
                            <td><?php echo ($logs['activity']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($logs['timestamp'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
