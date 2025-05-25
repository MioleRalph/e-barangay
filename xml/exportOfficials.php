<?php
 include('../connection.php');
// Headers for download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Officials_Accounts_List.xls");



// Check connection
if (!$connection) {
    die("Connection failed.");
}

// Fetch data from your database table using PDO
try {
    $sql = "SELECT * FROM accounts WHERE user_type = '3'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    // Output Excel headers
    echo "Account ID\t Profile Picture\t First Name\t Last Name\t Email \t Password\t Verification Token\t Status\t Date Registered\n";

    // Output data rows
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        foreach ($results as $row) {
            echo $row["account_id"] . "\t" .
                $row["profile_pic"] . "\t" .
                $row["first_name"] . "\t" .
                $row["last_name"] . "\t" .
                $row["email"] . "\t" .
                $row["password"] . "\t" .
                $row["verification_token"] . "\t" .
                $row["verification_status"] . "\t" .
                date('F g:i A', strtotime($row["date_registered"])) . "\n";
        }
    } else {
        echo "0 results";
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
$connection = null;
?>
