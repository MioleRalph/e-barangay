<?php
    include '../includes/official/official_sidebar.php';

    // Use a prepared statement to fetch logs
    $query = $connection->prepare("SELECT * FROM `logs` WHERE `user_id` = ?");
    $query->execute([$user_id]);
    $official_logs = $query->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

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
                        <th>Email</th>
                        <th>Activity</th>
                        <th>Timestamp</th>
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
                                    $activity = htmlspecialchars($logs['activity_type']);
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
