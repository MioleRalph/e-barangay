<?php
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include 'connection.php';
    session_start();

    // Check if user is logged in
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];

        // Fetch user details and role name
        $verify_user = $connection->prepare("
            SELECT accounts.*, roles.role_name
            FROM accounts
            INNER JOIN roles ON accounts.user_type = roles.id
            WHERE accounts.account_id = ?
            LIMIT 1
        ");
        $verify_user->execute([$user_id]);

        if ($verify_user->rowCount() > 0) {
            $user = $verify_user->fetch(PDO::FETCH_ASSOC);

            $name = $user['first_name'] . " " . $user['last_name'];
            $email = $user['email'];
            $user_type = $user['user_type']; // ID from accounts.user_type
            $activity_type = 'Logout'; // Fixed text for activity type

            // Insert logout activity log
            $log_stmt = $connection->prepare("
                INSERT INTO `logs` (`user_id`, `name`, `email`, `activity_type`, `user_type`, `timestamp`)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $log_stmt->execute([$user_id, $name, $email, $activity_type, $user_type]);
        } else {
            // User not found in accounts table
            echo "User not found.";
            exit;
        }
    }

    // Clear the cookies (logout)
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('role', '', time() - 3600, '/');

    // Clear session if used
    session_unset();
    session_destroy();

    // Redirect to landing page
    header('Location: index.php');
    exit();
?>
