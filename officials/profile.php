<?php 

    include '../includes/official/official_sidebar.php'; 

    $select_account = $connection->prepare("SELECT * FROM `accounts` WHERE account_id = ? LIMIT 1");
    $select_account->execute([$user_id]);
    $account = $select_account->fetch(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    $uploadDirectory = __DIR__ . '/../uploads/'; // Absolute path to 'images' directory

    // Check if the 'images' directory is writable. This is important to ensure we can upload files to it.
    if (!is_writable($uploadDirectory)) {
        echo "<script>Swal.fire('Error', 'Directory is not writable: $uploadDirectory', 'error');</script>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $file = isset($_FILES['image']) ? $_FILES['image'] : null;

        // Check if a file was uploaded and validate it
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
            $fileType = mime_content_type($file['tmp_name']);

            if (in_array($fileType, $allowedTypes)) {
                $fileName = uniqid('img_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                $filePath = __DIR__ . '/../uploads/' . $fileName;

                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $profilePic = $fileName; // Set the profile picture filename
                } else {
                    die('Failed to upload the image. Please try again.');
                }
            } else {
                die('Invalid file type. Only JPEG, PNG, WEBP, and GIF are allowed.');
            }
        } else {
            $profilePic = $account['profile_pic']; // Keep the existing profile picture if no new file is uploaded
        }

        $f_name = $_POST['f_name'];
        $l_name = $_POST['l_name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        $purok = $_POST['purok'];
        $dob = $_POST['birthdate'];

        if ($file) {
            $fileName = $profilePic;
        } else {
            $fileName = $account['image']; // Keep the existing image if no new file is uploaded
        }
    
        if (!empty($_POST['password'])) {
            // If the user provided a new password, hash it
            $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        } else {
            // If the password field is empty, keep the existing password
            $hashed_password = $account['password'];
        }
    
        // Prepare the update SQL query
        $update_sql = "UPDATE `accounts` SET `first_name` = ?, `last_name` = ?, `email` = ?, `date_of_birth` = ?, `purok` = ?, `contact_number` = ?, `profile_pic` = ?, `password` = ? WHERE `account_id` = ?";
        $stmt_update = $connection->prepare($update_sql);

        if ($stmt_update->execute([$f_name, $l_name, $email, $dob, $purok, $contact_number, $fileName, $hashed_password, $user_id])) {
            echo "<script>
                Swal.fire({
                    title: 'Success',
                    text: 'Profile updated successfully!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'profile.php';
                });
            </script>";
        } else {
            echo "<script>Swal.fire('Error', 'Failed to update profile. Please try again.', 'error');</script>";
        }
        exit;
    }    

?>

<section>
    <div class="container py-5">

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="<?php echo "../uploads/" . $account['profile_pic']; ?>" alt="avatar"
                            class="rounded-circle img-fluid" style="width: 150px;">
                        <h5 class="my-3"><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></h5>
                        <p class="text-muted mb-1"><?php echo ($account['email']); ?></p>
                        <p class="text-muted mb-4"><?php echo ($account['purok']); ?></p>
                        <div class="d-flex justify-content-center mb-2">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#profileModal">
                                Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- name row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Full Name</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></p>
                            </div>
                        </div>
                        <hr>

                        <!-- email row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Email</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?php echo ($account['email']); ?></p>
                            </div>
                        </div>
                        <hr>

                        <!-- mobile number row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Mobile</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?php echo ($account['contact_number']); ?></p>
                            </div>
                        </div>
                        <hr>

                        <!-- date of birth row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Birthdate</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    <?php 
                                        if (!empty($account['date_of_birth'])) {
                                            echo date('F j, Y', strtotime($account['date_of_birth']));
                                        } else {
                                            echo 'N/A';
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <hr>

                        <!-- purok row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Purok</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?php echo ($account['purok']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog d-flex align-items-center" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Edit Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="text-center mb-3">
                    <img src="<?php echo "../uploads/" . $account['profile_pic']; ?>" alt="profile" class="rounded-circle" width="150">
                </div>
                <form action="" method="post" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col">
                            <label for="f_name">First Name</label>
                            <input type="text" id="f_name" name="f_name" class="form-control" value="<?php echo ($account['first_name']); ?>" placeholder="First name">
                        </div>
                        <div class="col">
                            <label for="l_name">Last Name</label>
                            <input type="text" id="l_name" name="l_name" class="form-control" value="<?php echo ($account['last_name']); ?>" placeholder="Last name">
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo ($account['email']); ?>">
                    </div>

                    <!-- Contact Number Input -->
                    <div class="form-group">
                        <label for="contact_number">Mobile</label>
                        <input type="number" class="form-control" id="contact_number" name="contact_number" 
                            value="<?php echo ($account['contact_number']); ?>" 
                            maxlength="11" 
                            oninput="if(this.value.length > 11) this.value = this.value.slice(0,11);" 
                            pattern="\d{11}" 
                            placeholder="Enter 11-digit mobile number">
                    </div>

                    <!-- Birthdate Input -->
                    <div class="form-group">
                        <label for="birthdate">Birthdate</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo ($account['date_of_birth']); ?>">
                    </div>

                    <!-- Purok Input -->
                    <div class="form-group">
                        <label for="purok">Purok</label>
                        <input type="text" class="form-control" id="purok" name="purok" value="<?php echo ($account['purok']); ?>">
                    </div>

                    <!-- Password Input -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave empty to keep current password">
                    </div>

                    <!-- Image Input -->
                    <div class="form-group">
                        <label for="image" class="form-label">Upload Profile Picture</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="image" id="image" accept="image/*">
                            <label class="custom-file-label" for="image">Choose file</label>
                        </div>
                    </div>

                    <script>
                        // Update the label of the file input when a file is selected
                        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
                            var fileName = e.target.files[0]?.name || 'Choose file';
                            e.target.nextElementSibling.innerText = fileName;
                        });
                    </script>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>