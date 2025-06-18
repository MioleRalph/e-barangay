<?php
require '../connection.php';

$currentYear = date("Y");

// Get counts of new residents (user_type = 2) by month
$stmt = $connection->prepare("
    SELECT MONTH(date_registered) AS month_number, COUNT(*) AS total
    FROM accounts
    WHERE user_type = 2 AND YEAR(date_registered) = :year
    GROUP BY MONTH(date_registered)
");
$stmt->execute(['year' => $currentYear]);
$rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Full months list
$allMonths = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

$formattedData = [];
foreach ($allMonths as $num => $name) {
    $found = false;
    foreach ($rawData as $row) {
        if ((int)$row['month_number'] === $num) {
            $formattedData[] = ['month' => $name, 'total' => (int)$row['total']];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $formattedData[] = ['month' => $name, 'total' => 0];
    }
}

echo json_encode([
    'year' => $currentYear,
    'data' => $formattedData
]);
