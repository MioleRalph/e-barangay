<?php
include '../includes/resident/resident_sidebar.php';

$pageCategory = 'Itemized Monthly Collections and Disbursements'; // category for this page

// Fetch uploaded files for this category (read-only)
$query = $connection->prepare("SELECT * FROM uploaded_files WHERE file_category = ? ORDER BY uploaded_at DESC");
$query->execute([$pageCategory]);
$files = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Document Viewer</h1>
<p class="mb-4">
    View documents below. Supported formats: PDF, DOC, DOCX, XLS, XLSX.
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
        </h6>
    </div>
    <div class="card-body">
        <h5 class="font-weight-bold text-primary mb-3 text-center">Uploaded Files (<?= htmlspecialchars($pageCategory) ?>)</h5>

        <?php if (empty($files)): ?>
            <div class="alert alert-info">No files have been uploaded yet.</div>
        <?php else: ?>
            <?php foreach ($files as $file): ?>
                <?php
                    $filename = htmlspecialchars($file['filename']);
                    $filepathRaw = $file['file_path']; // raw path for filesystem checks
                    $filepath = htmlspecialchars($filepathRaw); // escaped for output
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

                    <?php if (file_exists($filepathRaw)): ?>
                        <?php if ($ext === 'pdf'): ?>
                            <iframe src="<?= $filepath; ?>" width="100%" height="500px" style="border:1px solid #ccc;"></iframe>

                        <?php elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx'])): ?>
                            <?php
                                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                                $absoluteUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($filepathRaw, './');
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
