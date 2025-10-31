<?php
include '../includes/official/official_sidebar.php';

$pageCategory = '20% Component of IRA Utilization'; // category for this page

if (isset($_POST['upload'])) {
    $fileName = basename($_FILES['file']['name']);
    $fileTmp  = $_FILES['file']['tmp_name'];
    $fileType = $_FILES['file']['type'];
    $uploadDir = '../uploads/';

    // $uploadDir = __DIR__ . '/../uploads/';


    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetPath = $uploadDir . $fileName;

    try {
        // Start transaction
        $connection->beginTransaction();

        // Fetch existing records for this category so we can remove files from disk
        $existingStmt = $connection->prepare("SELECT * FROM uploaded_files WHERE file_category = ?");
        $existingStmt->execute([$pageCategory]);
        $existingFiles = $existingStmt->fetchAll(PDO::FETCH_ASSOC);

        // Delete existing files from disk and DB (only this category)
        if (!empty($existingFiles)) {
            $delStmt = $connection->prepare("DELETE FROM uploaded_files WHERE file_category = ?");
            foreach ($existingFiles as $ef) {
                $existingPath = $ef['file_path'];
                if ($existingPath && file_exists($existingPath)) {
                    @unlink($existingPath); // remove old file (suppress warning)
                }
            }
            $delStmt->execute([$pageCategory]);
        }

        // Move uploaded file to target path
        if (!move_uploaded_file($fileTmp, $targetPath)) {
            // rollback and show error
            $connection->rollBack();
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'Unable to move uploaded file. Please try again.',
                    confirmButtonColor: '#d33'
                });
            </script>";
        } else {
            // Insert new record including file_category
            $stmt = $connection->prepare("INSERT INTO uploaded_files (filename, file_path, file_type, file_category) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fileName, $targetPath, $fileType, $pageCategory]);

            $connection->commit();

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'File Uploaded!',
                    text: 'Your file has been successfully uploaded and previous document(s) removed.',
                    confirmButtonColor: '#3085d6'
                });
            </script>";
        }
    } catch (Exception $e) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred during upload. Please try again.',
                confirmButtonColor: '#d33'
            });
        </script>";
    }
}

// Fetch uploaded files for this category
$query = $connection->prepare("SELECT * FROM uploaded_files WHERE file_category = ? ORDER BY uploaded_at DESC");
$query->execute([$pageCategory]);
$files = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Document Upload & Viewer</h1>
<p class="mb-4">
    Upload and preview your documents below. Supported formats: PDF, DOC, DOCX, XLS, XLSX.
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-upload"></i> Upload a File
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="form-group">
                <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="form-control-file" required>
            </div>
            <button type="submit" name="upload" class="btn btn-primary">
                <i class="fas fa-cloud-upload-alt"></i> Upload
            </button>
        </form>

        <hr>
        <h5 class="font-weight-bold text-primary mb-3 text-center">Uploaded Files (<?= htmlspecialchars($pageCategory) ?>)</h5>

        <?php if (empty($files)): ?>
            <div class="alert alert-info">No files have been uploaded yet.</div>
        <?php else: ?>
            <?php foreach ($files as $file): ?>
                <?php
                    $filename = htmlspecialchars($file['filename']);
                    $filepath = htmlspecialchars($file['file_path']);
                    $filetype = htmlspecialchars($file['file_type']);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                ?>
                <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong><?= $filename; ?></strong><br>
                            <small class="text-muted">
                                Uploaded on <?= date('F j, Y g:i A', strtotime($file['uploaded_at'])); ?>
                            </small>
                        </div>
                        <a href="<?= $filepath; ?>" download class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>

                    <?php if (file_exists($filepath)): ?>
                        <?php if ($ext === 'pdf'): ?>
                            <iframe src="<?= $filepath; ?>" width="100%" height="500px" style="border:1px solid #ccc;"></iframe>

                        <?php elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx'])): ?>
                            <?php
                                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                                $absoluteUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($filepath, './');
                            ?>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($absoluteUrl); ?>"
                                    width="100%" height="1000px" style="border:1px solid #ccc;"></iframe>

                        <?php else: ?>
                            <p class="text-muted">No preview available for this file type (<?= strtoupper($ext); ?>).</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-danger">File not found: <?= $filename ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
