<?php
 include('../connection.php');
// Headers for download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Benificiary_Records.xls");



// Check connection
if (!$connection) {
    die("Connection failed.");
}

// Fetch data from your database table using PDO
try {
    $sql = "SELECT * FROM beneficiaries ORDER BY updated_at DESC";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    // Output Excel headers
    echo "ID\t Account ID\t Address\t Contact Number\t Valid ID #\t Status\t Date Created\t Date Updated\n";

    // Output data rows
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        foreach ($results as $row) {
            echo $row["id"] . "\t" .
                $row["user_id"] . "\t" .
                $row["address"] . "\t" .
                $row["contact_number"] . "\t" .
                $row["valid_id_number"] . "\t" .
                $row["status"] . "\t" .
                date('F g:i A', strtotime($row["created_at"])) . "\t" .
                date('F g:i A', strtotime($row["updated_at"])) . "\n";
        }
    } else {
        echo "0 results";
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
$connection = null;
?>
