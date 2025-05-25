<?php

// Database connection
include '../../connection.php';

// Query to count residents
$residents_query = $connection->prepare("SELECT COUNT(*) AS total_residents FROM accounts WHERE user_type = 'resident'");
$residents_query->execute();
$residents_result = $residents_query->fetch(PDO::FETCH_ASSOC);
$total_residents = $residents_result['total_residents'];

// Query to count officials
$officials_query = $connection->prepare("SELECT COUNT(*) AS total_officials FROM accounts WHERE user_type = 'official'");
$officials_query->execute();
$officials_result = $officials_query->fetch(PDO::FETCH_ASSOC);
$total_officials = $officials_result['total_officials'];

// Pass data as JSON
echo json_encode([
    'residents' => $total_residents,
    'officials' => $total_officials
]);
?>

<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Fetch data from the PHP script
fetch('../../components/js/demo/chart-pie-data.php')
  .then(response => response.json())
  .then(data => {
    // Extract residents and officials count
    const totalResidents = data.residents;
    const totalOfficials = data.officials;

    // Pie Chart Example
    var ctx = document.getElementById("AccountsPieChart");
    var AccountsPieChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ["Residents", "Officials"],
        datasets: [{
          data: [totalResidents, totalOfficials], // Use dynamic data
          backgroundColor: ['#4e73df', '#1cc88a'],
          hoverBackgroundColor: ['#2e59d9', '#17a673'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: false
        },
        cutoutPercentage: 80,
      },
    });
  })
  .catch(error => console.error('Error fetching chart data:', error));
</script>