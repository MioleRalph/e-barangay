<?php 

    include '../includes/admin/admin_sidebar.php';
    $verified_accounts = $connection->query("SELECT * FROM `accounts` WHERE `verification_status` = 'verified' AND `user_type` = '2' ORDER BY `date_registered` DESC")->fetchAll(PDO::FETCH_ASSOC);

?>


<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Verified Resident Accounts</h1>
<p class="mb-4">
    This table shows verified resident accounts. You can quickly view account details, registration dates, and their verification status.
</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Verified Accounts</h6>
        <span class="badge badge-success"><?php echo count($verified_accounts); ?> Verified</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th style="width: 120px;">Account ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th style="width: 180px;">Date Registered</th>
                    </tr>
                </thead>

                <tfoot class="thead-dark">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th style="width: 120px;">Account ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th style="width: 180px;">Date Registered</th>
                    </tr>
                </tfoot>

                <tbody>
                    <?php
                    $count = 1;
                    foreach ($verified_accounts as $verified):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($verified['account_id']); ?></td>
                            <td><?php echo htmlspecialchars(decryptData($verified['first_name'])) . ' ' . (decryptData($verified['last_name'])); ?></td>
                            <td><?php echo htmlspecialchars($verified['email']); ?></td>
                            <td>
                                <span class="badge badge-success text-uppercase">
                                    <?php echo htmlspecialchars($verified['verification_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('F j, Y g:i A', strtotime($verified['date_registered'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .table thead th {
        vertical-align: middle;
        text-align: center;
    }
    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }
</style>

<?php include '../includes/footer.php'; ?>