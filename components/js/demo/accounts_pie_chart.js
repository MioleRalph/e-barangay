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