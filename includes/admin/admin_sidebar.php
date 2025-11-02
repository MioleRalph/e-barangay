<?php
// to display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include the database connection
include '../connection.php';

// include encryption functions
include '../encryption.php';


// Check if the user is logged in
if (isset($_COOKIE['user_id'])) {

    // Fetch user data from the database
    $user_id = $_COOKIE['user_id'];
    $query = $connection->prepare("SELECT * FROM accounts WHERE account_id = ?");
    $query->execute([$user_id]);
    $fetch_user = $query->fetch(PDO::FETCH_ASSOC);

    // Set session variables for first_name and email
    $_SESSION['full_name'] = $fetch_user['first_name'] . ' ' . $fetch_user['last_name'];

    // If no user is found, redirect to login
    if (!$fetch_user) {
        header('Location: ../login.php');
        exit();
    }
} else {
    // Redirect to login page if not logged in
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <?php
        // Get the current file name without extension
        $page = basename($_SERVER['PHP_SELF'], ".php");

        // Map file names to page titles
        $titles = [
            'admin_dashboard' => 'Admin Dashboard',
            'resident_verified_account' => 'Verified Residents',
            'resident_not_verified_account' => 'Not Verified Residents',
            'official_verified_account' => 'Verified Officials',
            'official_not_verified_account' => 'Not Verified Officials',
            'residents_account' => 'Residents Account',
            'resident_new_account' => 'Add Resident',
            'officials_account' => 'Officials Account',
            'official_new_account' => 'Add Official',
            'residents_logs' => 'Residents Logs',
            'officials_logs' => 'Officials Logs',
            'announcements' => 'Announcements',
            'residents_activity_history' => 'Residents Activity History',
            'officials_activity_history' => 'Officials Activity History',
            // Add more mappings as needed
        ];

        $title = isset($titles[$page]) ? $titles[$page] : 'Admin Panel';
    ?>
    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../components/css/sb-admin-2.min.css" rel="stylesheet">

    <link href="../components/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">E-Barangay</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <li class="nav-item">
                <a class="nav-link" href="announcements.php">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAccount_status"
                    aria-expanded="true" aria-controls="collapseAccount_status">
                    <i class="fas fa-fw fa-user-check"></i>
                    <span>Account Status</span>
                </a>

                <div id="collapseAccount_status" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Residents: </h6>
                        <a class="collapse-item" href="resident_verified_account.php"><i class="fas fa-user-check"></i> Verified</a>
                        <a class="collapse-item" href="resident_not_verified_account.php"><i class="fas fa-user-times"></i> Not Verified</a>

                        <div class="collapse-divider"></div>

                        <h6 class="collapse-header">Barangay Officials: </h6>

                        <a class="collapse-item" href="official_verified_account.php"><i class="fas fa-user-check"></i> Verified</a>
                        <a class="collapse-item" href="official_not_verified_account.php"><i class="fas fa-user-times"></i> Not Verified</a>
                    </div>
                </div>
            </li>

            <!-- Residents Information -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseResidents_info"
                    aria-expanded="true" aria-controls="collapseResidents_info">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Residents Information</span>
                </a>
                <div id="collapseResidents_info" class="collapse" aria-labelledby="headingResidents_info" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Residents:</h6>
                        <a class="collapse-item" href="residents_account.php"><i class="fas fa-address-book"></i> View Residents</a>
                        <a class="collapse-item" href="resident_new_account.php"><i class="fas fa-user-plus"></i> Add Resident</a>
                    </div>
                </div>
            </li>

            <!-- Barangay Officials Information -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOfficials_info"
                    aria-expanded="true" aria-controls="collapseOfficials_info">
                    <i class="fas fa-fw fa-user-tie"></i>
                    <span>Officials Information</span>
                </a>
                <div id="collapseOfficials_info" class="collapse" aria-labelledby="headingOfficials_info" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Manage Officials:</h6>
                        <a class="collapse-item" href="officials_account.php"><i class="fas fa-address-card"></i> View Officials</a>
                        <a class="collapse-item" href="official_new_account.php"><i class="fas fa-user-plus"></i> Add Official</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Activity History
            </div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseResidents_logs"
                    aria-expanded="true" aria-controls="collapseResidents_logs">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Residents</span>
                </a>
                <div id="collapseResidents_logs" class="collapse" aria-labelledby="headingResidents_logs" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Activity:</h6>
                        <a class="collapse-item" href="residents_logs.php"><i class="fas fa-sign-in-alt"></i> Login/Logout</a>
                        <a class="collapse-item" href="residents_activity_history.php"><i class="fas fa-list"></i> Activity History</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOfficials_logs"
                    aria-expanded="true" aria-controls="collapseOfficials_logs">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Officials</span>
                </a>
                <div id="collapseOfficials_logs" class="collapse" aria-labelledby="headingOfficials_logs" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Activity:</h6>
                        <a class="collapse-item" href="officials_logs.php"><i class="fas fa-sign-in-alt"></i> Login/Logout</a>
                        <a class="collapse-item" href="officials_activity_history.php"><i class="fas fa-list"></i> Activity History</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include '../includes/admin/topbar.php'; ?>