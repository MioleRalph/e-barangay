<?php
 include('../connection.php');
// Headers for download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Aid_Request_Records.xls");



// Check connection
if (!$connection) {
    die("Connection failed.");
}

// Fetch data from your database table using PDO
try {
    $sql = "SELECT * FROM aid_requests ORDER BY date_requested DESC";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    // Output Excel headers
    echo "ID\t Beneficiary ID\t Beneficiary Name\t Aid Type\t Request Reason\t Status\t Amount Requested\t Date Requested\t Date Approved\n";

    // Output data rows
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        foreach ($results as $row) {
            echo $row["id"] . "\t" .
                $row["beneficiary_id"] . "\t" .
                $row["beneficiary_name"] . "\t" .
                $row["aid_type"] . "\t" .
                $row["request_reason"] . "\t" .
                $row["status"] . "\t" .
                $row["amount_requested"] . "\t" .
                date('F g:i A', strtotime($row["date_requested"])) . "\t" .
                date('F g:i A', strtotime($row["date_approved"])) . "\n";
        }
    } else {
        echo "0 results";
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
$connection = null;
?>
