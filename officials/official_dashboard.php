<?php include '../includes/official/official_sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Welcome, Barangay Official!</h1>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Welcome Card -->
    <div class="col-xl-12 col-md-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body text-center">
                <h4 class="font-weight-bold text-primary">Welcome to the E-Barangay System</h4>
                <p class="mt-3">We are glad to have you here! Use this platform to manage barangay-related tasks efficiently.</p>
                <a href="#" class="btn btn-primary mt-3">Get Started</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links Row -->
<div class="row">

    <!-- Quick Link 1 -->
    <div class="col-lg-4 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                Manage Requests
                <div class="text-white-50 small">View and handle pending requests</div>
                <a href="#" class="btn btn-light btn-sm mt-2">Go to Requests</a>
            </div>
        </div>
    </div>

    <!-- Quick Link 2 -->
    <div class="col-lg-4 mb-4">
        <div class="card bg-info text-white shadow">
            <div class="card-body">
                View Reports
                <div class="text-white-50 small">Generate and review reports</div>
                <a href="#" class="btn btn-light btn-sm mt-2">Go to Reports</a>
            </div>
        </div>
    </div>

    <!-- Quick Link 3 -->
    <div class="col-lg-4 mb-4">
        <div class="card bg-warning text-white shadow">
            <div class="card-body">
                Update Profile
                <div class="text-white-50 small">Edit your account details</div>
                <a href="profile.php" class="btn btn-light btn-sm mt-2">Go to Profile</a>
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

<!-- Footer -->
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
