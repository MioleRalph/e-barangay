<?php
session_start();
include 'connection.php';

include ('includes/alerts.php');

// Check if the token is provided in the URL
if (isset($_GET['token'])) {

    $token = $_GET['token'];

    // Prepare the query to check if the token exists in the database
    $verification_token = $connection->prepare("SELECT `verification_token`, `verification_status` FROM `accounts` WHERE `verification_token` = ? LIMIT 1");
    $verification_token->execute([$token]);

    // Check if a row was returned (token exists)
    if ($verification_token->rowCount() > 0) {

        // Fetch the token value
        $row = $verification_token->fetch(PDO::FETCH_ASSOC);

        if ($row['verification_status'] == "not verified") {

            // Update the verification_status to 'verified'
            $update_status = $connection->prepare("UPDATE accounts SET `verification_status` = 'verified' WHERE `verification_token` = ? LIMIT 1");
            $update_status->execute([$token]);

            if($update_status){
                $_SESSION['status'] = 'Your account has been verified';
                header('Location: login.php');
                exit;
            }

            else{
                $_SESSION['status'] = 'Verification Failed';
                header('Location: login.php');
                exit;
            }

        } else {
            $_SESSION['status'] = 'Email Already Verified. Please Login';
            header('Location: login.php');
            exit;
        }

    } else {
        $_SESSION['status'] = 'This token does not exist';
        header('Location: login.php');
        exit;
    }

} else {
    $_SESSION['status'] = 'Not Allowed';
    header('Location: login.php');
    exit;
}
?>
