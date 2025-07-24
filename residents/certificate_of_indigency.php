<?php 

    include '../includes/resident/resident_sidebar.php'; 
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    $select_account = $connection->prepare("SELECT * FROM `accounts` WHERE account_id = ? LIMIT 1");
    $select_account->execute([$user_id]);
    $account = $select_account->fetch(PDO::FETCH_ASSOC);

        if (isset($_POST['submit'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $address = $_POST['address'];
        $amount = 0; 
        $status = 'Pending';
        $request_type = 'Certificate of Indigency';

        // Correct order: name, date_of_birth, email, address, amount, date_submitted
        $insert = $connection->prepare("INSERT INTO `file_request` (`user_id`, `name`, `date_of_birth`, `email`, `address`, `amount`, `transaction_type`, `transaction_status`, `date_submitted`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $insert->execute([$user_id, $full_name, $dob, $email, $address, $amount, $request_type, $status]);

        // Insert log into resident_request_logs
        $log_stmt = $connection->prepare("INSERT INTO `resident_request_logs` (`account_id`, `name`, `activity`, `activity_type`, `timestamp`) VALUES (?, ?, ?, ?, NOW())");
        $log_stmt->execute([$user_id, $full_name, 'Requested a Certificate of Indigency', 'Certificate of Indigency']);

        echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Your request has been submitted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'certificate_of_indigency.php';
                    });
                </script>";
    }

?>

<section>
    <div class="container py-5">
        <h1 class="text-center mb-4">Certificate of Indigency</h1>
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="../components/img/undraw_profile_1.svg" alt="avatar"
                            class="rounded-circle img-fluid mb-3" style="width: 120px;">
                        <h5 class="mb-1"><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></h5>
                        <p class="text-muted mb-1"><?php echo ($account['email']); ?></p>
                        <p class="text-muted mb-2">
                            <?php echo isset($account['address']) ? ($account['address']) : 'No address provided'; ?>
                        </p>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <!-- name row -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="full_name" class="mb-0">Full Name</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo ($account['first_name'] . ' ' . $account['last_name']); ?>" required>
                                </div>
                            </div>

                            <!-- email row -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="email" class="mb-0">Email</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $account['email']; ?>" required>
                                </div>
                            </div>

                            <!-- mobile number row -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="dob" class="mb-0">Date Of Birth</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo isset($account['date_of_birth']) ? ($account['date_of_birth']) : 'No date provided'; ?>" required>
                                </div>
                            </div>

                            <!-- address row -->
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label for="address" class="mb-0">Address</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($account['address']) ? ($account['address']) : 'No address provided'; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <button type="submit" name="submit" class="btn btn-primary btn-block font-weight-bold py-2 mt-4">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include '../includes/footer.php'; ?>



