<?php
    session_start();
    include 'connection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_reset'])) {
        $email = $_POST['email'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $token = $_POST['password_token'];
    
        if (!empty($token)) {
            if (!empty($email) && !empty($new_password) && !empty($confirm_password)) {
                $check_token = $connection->prepare("SELECT `verification_token` FROM `accounts` WHERE `verification_token` = ? LIMIT 1");
                $check_token->execute([$token]);
    
                if ($check_token->rowCount() > 0) {
                    if ($new_password === $confirm_password) {
                        $update_password = $connection->prepare("UPDATE `accounts` SET `password` = ? WHERE `verification_token` = ? LIMIT 1");
                        $update_password->execute([$new_password, $token]);
    
                        if ($update_password) {

                            $new_token = md5(rand()) . "changed";

                            $update_password = $connection->prepare("UPDATE `accounts` SET `verification_token` = ? WHERE `verification_token` = ? LIMIT 1");
                            $update_password->execute([$new_token, $token]);

                            $_SESSION['status'] = "New password successfully updated";
                            header('Location: login.php');
                            exit;
                        } else {
                            $_SESSION['status'] = "Password update failed. Try again.";
                            header("Location: password_reset.php?token=$token&email=$email");
                            exit;
                        }
                    } else {
                        $_SESSION['status'] = "Password and Confirm Password do not match!";
                        header("Location: password_reset.php?token=$token&email=$email");
                        exit;
                    }
                } else {
                    $_SESSION['status'] = "Invalid token.";
                    header("Location: password_reset.php?token=$token&email=$email");
                    exit;
                }
            } else {
                $_SESSION['status'] = "All fields are required.";
                header("Location: password_reset.php?token=$token&email=$email");
                exit;
            }
        } else {
            $_SESSION['status'] = "Token not available.";
            header('Location: password_reset.php');
            exit;
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

    <title>Barangay - Forgot Password</title>

    <!-- Fontawesom CDN link -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="components/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <!-- Navbar -->
    <?php include 'includes/home_navbar.php'; ?>
    <!-- End of Navbar -->


    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">

                    <?php
                        if (isset($_SESSION['status'])) {
                        ?>
                            <div class="alert alert-success">
                                <h5><?= $_SESSION['status'] ?></h5>
                            </div>
                        <?php
                            unset($_SESSION['status']);
                        }
                    ?>
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                                        <p class="mb-4">We get it, stuff happens. Just enter your email address below
                                            and we'll send you a link to reset your password!</p>
                                    </div>
                                    <form class="user" action="password_reset.php" method="POST">

                                        <input type="hidden" name="password_token" value="<?php if(isset($_GET['token'])) {echo $_GET['token']; } ?>">

                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." value="<?php if(isset($_GET['email'])) {echo $_GET['email']; } ?>" readonly>
                                        </div>

                                        <div class="form-group">
                                            <input type="password" name="new_password" class="form-control form-control-user" id="new_password" placeholder="Enter New Password">
                                        </div>

                                        <div class="form-group">
                                            <input type="password" name="confirm_password" class="form-control form-control-user" id="confirm_password" placeholder="Confirm Password">
                                        </div>

                                        <button type="submit" name="password_reset" class="btn btn-primary btn-user btn-block">
                                            Reset Password
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Already have an account? Login!</a>
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