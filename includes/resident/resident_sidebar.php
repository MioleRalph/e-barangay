<?php
    // to display errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Start the session
    session_start();

    // Include the database connection
    include '../connection.php';


    // Check if the user is logged in
    if (isset($_COOKIE['user_id'])) {

        // Fetch user data from the database
        $user_id = $_COOKIE['user_id'];
        $query = $connection->prepare("SELECT * FROM accounts WHERE account_id = ?");
        $query->execute([$user_id]);
        $fetch_user = $query->fetch(PDO::FETCH_ASSOC);

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
        // Set default title
        $pageTitle = 'Resident Dashboard';

        // Get current script name
        $currentPage = basename($_SERVER['PHP_SELF']);

        // Map filenames to titles
        $titles = [
            'resident_dashboard.php' => 'Resident Dashboard',
            'barangay_clearance.php' => 'Barangay Clearance Request',
            'certificate_of_indigency.php' => 'Certificate of Indigency Request',
            'certificate_of_residency.php' => 'Certificate of Residency Request',
            'blotter.php' => 'Blotter Request',
            'view_events.php' => 'Events',
            'view_emergency.php' => 'Emergency Announcements',
            'view_health.php' => 'Health Announcements',
            'view_public_notice.php' => 'Public Notices',
            'view_lost_and_found.php' => 'Lost & Found',
            'view_job_posting.php' => 'Job Postings',
            'view_community_project.php' => 'Community Projects',
            'request_history.php' => 'Request History',
            'request_status.php' => 'Request Status',
            // Add more mappings as needed
        ];

        if (isset($titles[$currentPage])) {
            $pageTitle = $titles[$currentPage];
        }
    ?>
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

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



</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="resident_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">E-Barangay</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="resident_dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Assistance Requests page -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAssistance"
                    aria-expanded="true" aria-controls="collapseAssistance">
                    <i class="fas fa-file-signature"></i>
                    <span>Requests</span>
                </a>
                <div id="collapseAssistance" class="collapse" aria-labelledby="headingAssistance"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Requests:</h6>
                        <a class="collapse-item" href="barangay_clearance.php"><i class="fas fa-file-alt"></i> Barangay clearance</a>
                        <a class="collapse-item" href="certificate_of_indigency.php"><i class="fas fa-certificate"></i> Certificate of indigency</a>
                        <a class="collapse-item" href="certificate_of_residency.php"><i class="fas fa-id-card"></i> Certificate of residency</a>
                        <a class="collapse-item" href="blotter.php"><i class="fas fa-book"></i> Blotter</a>
                    </div>
                </div>
            </li>

            <!-- Announcements page -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAnnouncements"
                    aria-expanded="true" aria-controls="collapseAnnouncements">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
                <div id="collapseAnnouncements" class="collapse" aria-labelledby="headingAnnouncements"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">View Announcement:</h6>
                        <a class="collapse-item" href="view_financial_assistance.php"><i class="fas fa-coins"></i> Financial Assistance</a>
                        <a class="collapse-item" href="view_events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                        <a class="collapse-item" href="view_emergency.php"><i class="fas fa-exclamation-triangle"></i> Emergency</a>
                        <a class="collapse-item" href="view_health.php"><i class="fas fa-heartbeat"></i> Health</a>
                        <a class="collapse-item" href="view_public_notice.php"><i class="fas fa-bell"></i> Public Notice</a>
                        <a class="collapse-item" href="view_lost_and_found.php"><i class="fas fa-search"></i> Lost & Found</a>
                        <a class="collapse-item" href="view_job_posting.php"><i class="fas fa-briefcase"></i> Job Postings</a>
                        <a class="collapse-item" href="view_community_project.php"><i class="fas fa-users"></i> Community Projects</a>
                    </div>
                </div>
            </li>

            <!-- request history page -->
            <li class="nav-item">
                <a class="nav-link" href="request_history.php">
                    <i class="fas fa-history"></i>
                    <span>Request History</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="request_status.php">
                    <i class="fas fa-history"></i>
                    <span>Request Status</span>
                </a>
            </li>

            <!-- barangay officials page -->
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-user-tie"></i>
                    <span>Barangay officials</span>
                </a>
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

            <?php include '../includes/resident/topbar.php'; ?>
