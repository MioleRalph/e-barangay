<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="E-Barangay System" />
    <meta name="author" content="Barangay Team" />
    <title>E-Barangay System</title>
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="components/css/landingPage.css" rel="stylesheet" />
</head>

<body id="page-top" class="bg-gradient-primary">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#page-top">E-Barangay System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars ms-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="#about">About</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="#team">Barangay Officials</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Masthead-->
    <header class="masthead">
        <div class="container text-center">
            <img src="components/img/stock_image/brgy_logo_nobg.png" alt="Barangay Logo" class="mb-4" style="max-width: 300px;">
            <div class="masthead-subheading">Welcome to E-Barangay System!</div>
            <div class="masthead-heading text-uppercase">Your Partner in Community Services</div>
            <a class="btn btn-primary btn-xl text-uppercase" href="#services">Learn More</a>
        </div>
    </header>
    <!-- Services-->
    <section class="page-section" id="services">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Services</h2>
                <h3 class="section-subheading text-white">Providing essential services to the community.</h3>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-id-card fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Barangay Clearance</h4>
                    <p class="text-white">Get your barangay clearance quickly and efficiently.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-users fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Community Programs</h4>
                    <p class="text-white">Participate in various programs for community development.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-hand-holding-heart fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Assistance Services</h4>
                    <p class="text-white">Avail of financial, medical, and other assistance services.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About-->
    <section id="about" class="page-section p-0">
        <div id="aboutCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <!-- Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="1"></button>
            </div>

            <!-- Slides -->
            <div class="carousel-inner" style="max-height: 500px; overflow: hidden;">
                <div class="carousel-item active">
                    <img src="components/img/about/1.jpg" class="d-block w-100" alt="Our Barangay’s Foundation"
                        style="height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h5 class="text-uppercase">1990 – Our Barangay’s Foundation</h5>
                        <p>Established to serve and support the community.</p>
                    </div>
                </div>

                <div class="carousel-item">
                    <img src="components/img/about/2.jpg" class="d-block w-100" alt="Growth and Development"
                        style="height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h5 class="text-uppercase">2000 – Growth and Development</h5>
                        <p>Expanded services to meet the growing needs of residents.</p>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        </div>
    </section>

    <!-- Barangay Officials -->
    <section class="page-section bg-dark" id="team">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase text-white">Our Barangay Officials</h2>
                <h3 class="section-subheading text-white">Meet the people who serve the community.</h3>
            </div>

            <!-- BARANGAY CAPTAIN -->
            <div class="row">
                <div class="team-member">
                    <img class="mx-auto rounded-circle" src="components/img/stock_image/brgy_captain.png" alt="Justino E. Balbon" />
                    <h4 class="text-white">Justino E. Balbon</h4>
                    <p class="text-white">Barangay Captain</p>
                </div>
            </div>


            <div class="row">

                <!-- BARANGAY SECRETARY -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/brgy_secretary.jpg" alt="Wilma P. Carbonilla" />
                        <h4 class="text-white">Wilma P. Carbonilla</h4>
                        <p class="text-white">Barangay Secretary</p>
                    </div>
                </div>

                <!-- BARANGAY TREASURER -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/brgy_treasurer.jpg" alt="Alma Vida Galon" />
                        <h4 class="text-white">Alma Vida Galon</h4>
                        <p class="text-white">Barangay Treasurer</p>
                    </div>
                </div>

                <!-- BARANGAY SK CHAIRPERSON -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/sk_chairperson_jonathan_b_refugio.jpg" alt="Jonathan B. Refugio" />
                        <h4 class="text-white">Jonathan B. Refugio</h4>
                        <p class="text-white">Barangay SK Chairperson</p>
                    </div>
                </div>

                <!-- 1ST SBM -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/1st_sbm_roly_beringuel.jpg" alt="Roly Beringuel" />
                        <h4 class="text-white">Roly Beringuel</h4>
                        <p class="text-white">1st Sangguniang Barangay Member</p>
                    </div>
                </div>

                <!-- 2ND SBM -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/2nd_sbm_francisco_tabale.jpg" alt="Francisco Tabale" />
                        <h4 class="text-white">Francisco Tabale</h4>
                        <p class="text-white">2nd Sangguniang Barangay Member</p>
                    </div>
                </div>

                <!-- 3RD SBM -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/3rd_sbm_emily_s_alcober.jpeg" alt="Emily S. Alcober" />
                        <h4 class="text-white">Emily S. Alcober</h4>
                        <p class="text-white">3rd Sangguniang Barangay Member</p>
                    </div>
                </div>

                <!-- 4TH SBM -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/4th_sbm_garrick_olayer.jpg" alt="Garrick Olayer" />
                        <h4 class="text-white">Garrick Olayer</h4>
                        <p class="text-white">4th Sangguniang Barangay Member</p>
                    </div>
                </div>

                <!-- 5TH SBM -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/5th_sbm_marcelo_jovita.jpg" alt="Marcelo Juvita" />
                        <h4 class="text-white">Marcelo Juvita</h4>
                        <p class="text-white">5th Sangguniang Barangay Member</p>
                    </div>
                </div>

                <!-- 6TH SBM -->
                <div class="col-lg-4">
                    <div class="team-member">
                        <img class="mx-auto rounded-circle" src="components/img/stock_image/6th_sbm_danilo_bangkr.jpg" alt="Danilo Bangkr" />
                        <h4 class="text-white">Danilo Bangkr</h4>
                        <p class="text-white">6th Sangguniang Barangay Member</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer-->
    <footer class="footer py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 text-lg-start text-white">E-Barangay System 2025</div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="components/js/landingPage.js"></script>
</body>

</html>