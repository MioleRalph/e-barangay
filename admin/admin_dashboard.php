<?php 
    include '../includes/admin/admin_sidebar.php'; 

?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Residents Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Residents</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">1,234</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Households Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Households</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">456</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-home fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barangay Officials Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Barangay Officials</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Requests</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Content Row -->
    <div class="row">
        <!-- Bar Chart for New Residents -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">New Residents This Year (Monthly)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 250px; overflow: hidden;">
                        <canvas id="residentsBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Blotter Reports This Year (Monthly)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 250px; overflow: hidden;">
                        <canvas id="BlotterBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-5">
        <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Announcement Categories Overview</h6>
            </div>
            <div class="card-body mb">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="announcementPieChart" style="max-width: 100%; max-height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>


<?php include '../includes/footer.php'; ?>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- get_resident_chart_data.php -->
<script>
    fetch('get_resident_chart_data.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.data.map(item => item.month);
            const values = data.data.map(item => item.total);

            const ctx = document.getElementById('residentsBarChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'New Residents',
                        data: values,
                        backgroundColor: '#4e73df',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 10,
                            bottom: 10
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly New Residents - ' + data.year
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
</script>

<!-- get_blotter_chart_data.php -->
<script>
    fetch('get_blotter_chart_data.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.data.map(item => item.month);
            const values = data.data.map(item => item.total);

            const ctx = document.getElementById('BlotterBarChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Blotter Reports',
                        data: values,
                        backgroundColor: '#4e73df',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 10,
                            bottom: 10
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Blotter Reports - ' + data.year
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
</script>

<!-- get_announcement_chart_data.php -->
<script>
    fetch('get_announcement_chart_data.php')
    .then(res => res.json())
    .then(data => {
        const labels = data.map(item => item.category);
        const values = data.map(item => item.total);

        const ctx = document.getElementById('announcementPieChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Announcements by Category',
                    data: values,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                        '#e74a3b', '#858796', '#20c9a6', '#fd7e14'
                    ],
                    hoverOffset: 10,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            boxWidth: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                },
                layout: {
                    padding: 10
                }
            }
        });
    });
</script>