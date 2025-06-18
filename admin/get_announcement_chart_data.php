<?php
require '../connection.php'; // Ensure this connects correctly

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get counts grouped by category, only for active announcements
$stmt = $connection->prepare("
    SELECT category, COUNT(*) AS total
    FROM announcements
    WHERE status = 'active'
    GROUP BY category
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define expected categories
$allCategories = [
    'Health', 'Financial Assistance', 'Events', 'Emergency',
    'Public Notice', 'Lost & Found', 'Job Postings', 'Community Projects'
];

$output = [];

foreach ($allCategories as $category) {
    $found = false;
    foreach ($data as $row) {
        if ($row['category'] === $category) {
            $output[] = ['category' => $category, 'total' => (int)$row['total']];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $output[] = ['category' => $category, 'total' => 0];
    }
}

echo json_encode($output);
?>
