<?php
    include '../includes/official/official_sidebar.php';

    $beneficiary_list = $connection->query("SELECT * FROM `beneficiaries` ORDER BY `updated_at` DESC")->fetchAll(PDO::FETCH_ASSOC);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    // Delete Beneficiaries
    if (isset($_POST['delete_beneficiaries'])) {
        $delete_id = $_POST['delete_id'];

        $verify_delete = $connection->prepare("SELECT * FROM `beneficiaries` WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_beneficiaries = $connection->prepare("DELETE FROM `beneficiaries` WHERE id = ?");
            if ($delete_beneficiaries->execute([$delete_id])) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Beneficiary has been deleted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'beneficiaries.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error deleting beneficiary.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Beneficiary already deleted!',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>";
        }
    }

?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Beneficiaries List</h1>

<p class="mb-4">
    Below is a list of all registered beneficiaries. You can update or delete records as needed. Use the "Download" button to export the data.
</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Beneficiary List</h6>
        <a href="../xml/export_beneficiaries.php" class="btn btn-primary btn-sm">
            <i class="fas fa-file-export"></i> Download
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Account ID</th>
                        <th>address</th>
                        <th>contact_number</th>
                        <th>valid_id_number</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Account ID</th>
                        <th>address</th>
                        <th>contact_number</th>
                        <th>valid_id_number</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($beneficiary_list as $beneficiaries):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo ($beneficiaries['user_id']); ?></td>
                            <td><?php echo ($beneficiaries['address']); ?></td>
                            <td><?php echo ($beneficiaries['contact_number']); ?></td>
                            <td><?php echo ($beneficiaries['valid_id_number']); ?></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($beneficiaries['updated_at'])); ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <form method="POST" action="" class="delete-form me-2">
                                        <input type="hidden" name="delete_id" value="<?php echo ($beneficiaries['id']); ?>">
                                        <input type="hidden" name="delete_beneficiaries" value="1">
                                        <button type="button" class="btn btn-danger btn-sm delete-btn">Delete</button>
                                    </form>
                                    <form method="GET" action="update_beneficiaries.php">
                                        <input type="hidden" name="update_id" value="<?php echo ($beneficiaries['id']); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php include '../includes/footer.php'; ?>


<!-- DELETE SWEETALERT2 -->
<script>
    // Delete confirmation
        $('.delete-btn').on('click', function() {
            const form = $(this).closest('.delete-form');
            const reviewId = form.find('input[name="delete_id"]').val();

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log("Deleting log ID: " + reviewId); // Debug log
                    form.submit(); // Submit the form if confirmed
                }
            });
        });
</script>