<?php include('../adminHeader.php'); ?>

        <div class="container-fluid px-4">
            <h1 class="mt-4 text-center">Export Data</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            <div class="row">
                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Users </h3>
                            <?php

                            $query = "SELECT * FROM user WHERE user_type = 'HypeBeast user' ";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importUser.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Sellers </h3>
                            <?php

                            $query = "SELECT * FROM user WHERE pass = '1234' ";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importSeller.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Staffs </h3>
                            <?php

                            $query = "SELECT * FROM staff";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importStaff.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Products </h3>
                            <?php

                            $query = "SELECT * FROM product";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importProduct.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Orders</h3>
                            <?php

                            $query = "SELECT * FROM `approved_order`";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importOrder.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>


                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>Job Lists</h3>
                            <?php

                            $query = "SELECT * FROM job_lists";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importJob.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Applicants</h3>
                            <?php

                            $query = "SELECT * FROM pending_applicants";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importApplicants.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Staffs</h3>
                            <?php

                            $query = "SELECT * FROM job_lists";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importStaff.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Logs</h3>
                            <?php

                            $query = "SELECT * FROM logs WHERE system_type = 'HypeBeast'";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importLogs.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-4">
                    <div class="card mb-4" style="background-color: #9BB7D4">
                        <div class="card-body text-center">
                            <h3>List of Reviews</h3>
                            <?php

                            $query = "SELECT * FROM review_table";
                            $run_query = mysqli_query($connforMyOnlineDb, $query);

                            $row = mysqli_num_rows($run_query);

                            echo '<h1>' . $row . '</h1>'
                            ?>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #3D5E8C">
                        <a href="importRate.php" class="btn btn-primary">IMPORT</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
<?php include('../includes/footer.php'); ?>
<?php include('../includes/script.php'); ?>