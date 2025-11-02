<?php
    // Display errors for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database connection
    include 'connection.php';

    // Include encryption functions
    include 'encryption.php'; 
    session_start();

    $warning_msg = []; // Initialize the warning message array

    if (isset($_POST['submit'])) {
        $email = trim($_POST['email']);
        $pass = trim($_POST['password']);

        if (!empty($email) && !empty($pass)) {

            // Fetch from accounts JOIN roles table
            $query = $connection->prepare("
                SELECT accounts.*, roles.role_name, accounts.approval_status
                FROM accounts
                INNER JOIN roles ON accounts.user_type = roles.id
                WHERE accounts.email = ? 
                  AND accounts.verification_status = 'verified' 
                  AND accounts.approval_status = 'approved'
                LIMIT 1
            ");
            $query->execute([$email]);

            if ($query->rowCount() > 0) {
                $fetch = $query->fetch(PDO::FETCH_ASSOC);

                if (password_verify($pass, $fetch['password'])) {
                    $id = $fetch['account_id'];
                    $name = $fetch['first_name'] . " " . $fetch['last_name'];
                    $activity_type = 'login';
                    $user_type = $fetch['user_type']; // 'administrator', 'official', 'resident'
                    $role = $fetch['role_name'];

                    $encrypted_name = encryptData($name);
                    $encrypted_activity_type = encryptData($activity_type);

                    // Log user login activity
                    $log_stmt = $connection->prepare("INSERT INTO `logs` (`user_id`, `name`, `email`, `activity_type`, `user_type`, `timestamp`)
                        VALUES (?, ?, ?, ?, ?, NOW())");
                    $log_stmt->execute([$id, $encrypted_name, $email, $encrypted_activity_type, $user_type]);

                    // Set session or cookie
                    setcookie('user_id', $id, 0, '/');
                    setcookie('role', $role, 0, '/');

                    // Redirect based on role
                    if ($role === 'administrator') {
                        header('Location: admin/admin_dashboard.php');
                    } elseif ($role === 'resident') {
                        header('Location: residents/resident_dashboard.php');
                    } elseif ($role === 'official') {
                        header('Location: officials/official_dashboard.php');
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Role',
                                text: 'Invalid user role detected!'
                            }).then(() => {
                                window.location.href = 'login.php';
                            });
                        </script>";
                    }
                    exit();

                } else {
                    // Incorrect password
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Incorrect Password',
                            text: 'The password you entered is incorrect!'
                        }).then(() => {
                            window.location.href = 'login.php';
                        });
                    </script>";
                }
            } else {
                // No account found
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'User Not Found',
                        text: 'No verified account found with that email address!'
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                </script>";
            }
        } else {
            $warning_msg[] = "Please fill in all fields.";
        }
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

    <title>Barangay - Login</title>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="components/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Barangay System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End of Navbar -->


    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>

                                    <form class="user" action="login.php" method="POST">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Enter Email Address...">
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                                        </div>

                                        <div class="input-box button">
                                            <input type="Submit" name="submit" value="Login" class="btn btn-primary btn-user btn-block">
                                        </div>
                                        <hr>
                                        
                                    </form>
                                    <div class="text-center">
                                        <a class="small" href="forgot_password.php">Forgot Password?</a>
                                    </div>

                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>

                                    <div class="text-center">
                                        <a class="small" href="resend_email_verification.php">Did not recieve email verification? Resend</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <?php include 'includes/scripts.php'; ?>
