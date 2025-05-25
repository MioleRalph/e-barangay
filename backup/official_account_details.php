<?php 

    include '../includes/admin/admin_sidebar.php'; 

    $id = $_GET['account_id'] ?? null;
    if (!$id) {
        die("No user ID provided.");
    }
    // Fetch user details
    $select_account = $connection->prepare("SELECT * FROM `accounts` WHERE account_id = ? LIMIT 1");
    $select_account->execute([$id]);
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
        $update_sql = "UPDATE `accounts` SET `first_name` = ?, `last_name` = ?, `email` = ?, `profile_pic` = ?, `password` = ? WHERE `account_id` = ?";
        $stmt_update = $connection->prepare($update_sql);
    
        if ($stmt_update->execute([$f_name, $l_name, $email, $fileName, $hashed_password, $id])) {
            echo "<script>
                Swal.fire({
                    title: 'Success',
                    text: 'Profile updated successfully!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'official_account_details.php?account_id=$id';
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
            <div class="col">
                <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">User</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="<?php echo "../uploads/" . $account['profile_pic']; ?>" alt="avatar"
                            class="rounded-circle img-fluid" style="width: 150px;">
                        <h5 class="my-3"><?php echo ($account['first_name'] . ' ' . $account['last_name']); ?></h5>
                        <p class="text-muted mb-1">Full Stack Developer</p>
                        <p class="text-muted mb-4">Bay Area, San Francisco, CA</p>
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
                                <p class="text-muted mb-0">(098) 765-4321</p>
                            </div>
                        </div>
                        <hr>

                        <!-- address row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Address</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">Bay Area, San Francisco, CA</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4 mb-md-0">
                            <div class="card-body">
                                <p class="mb-4"><span class="text-primary font-italic me-1">assigment</span> Project Status
                                </p>
                                <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                                <div class="progress rounded mb-2" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4 mb-md-0">
                            <div class="card-body">
                                <p class="mb-4"><span class="text-primary font-italic me-1">assigment</span> Project Status
                                </p>
                                <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                                <div class="progress rounded" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                                <div class="progress rounded mb-2" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


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
