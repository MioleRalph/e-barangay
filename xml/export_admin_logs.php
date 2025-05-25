<?php
 include('../connection.php');
// Headers for download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Admin_Logs.xls");



// Check connection
if (!$connection) {
    die("Connection failed.");
}

// Fetch data from your database table using PDO
try {
    $sql = "SELECT * FROM logs WHERE user_type = '1' ORDER BY updated_at DESC";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    // Output Excel headers
    echo "ID\t Account ID\t Account Name\t Email\t Activity Type\t Timestamp\n";

    // Output data rows
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        foreach ($results as $row) {
            echo $row["log_id"] . "\t" .
                $row["user_id"] . "\t" .
                $row["name"] . "\t" .
                $row["email"] . "\t" .
                $row["activity_type"] . "\t" .
                date('F g:i A', strtotime($row["timestamp"])) . "\n";
        }
    } else {
        echo "0 results";
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
$connection = null;
?>
