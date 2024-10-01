<?php

// CSRF Token generation (if not already set)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include('layout/header.php');
include('layout/asidebar.php');

$targetDir = "uploads/" . $_SESSION['username'] . "/";

// Function to sanitize file names
function sanitizeFileName($fileName) {
    return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
}

// Function to sanitize output to prevent XSS
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the CSRF token is set and validate it
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Handle delete operation
    if (isset($_POST['delete'])) {
        $fileToDelete = sanitizeFileName($_POST['file']);
        if (file_exists($targetDir . $fileToDelete)) {
            unlink($targetDir . $fileToDelete);
            $success = "File deleted successfully.";
        } else {
            $error = "File does not exist.";
        }
    } else {
        // Handle file upload operation
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = sanitizeFileName(basename($_FILES['fileToUpload']['name']));
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validate file type and size
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($_FILES['fileToUpload']['tmp_name']);

        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            $error = "File size exceeds 5MB.";
        } elseif (!in_array($fileMimeType, $allowedMimeTypes)) {
            $error = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFilePath)) {
                $success = "File uploaded successfully.";
            } else {
                $error = "Error uploading the file.";
            }
        }
    }
}

// Scan user directory for uploaded files
$uploadedFiles = array_diff(scandir($targetDir), array('.', '..'));

?>

<div class="dashboard-main-wrapper">
    <?php include('layout/top-bar.php'); ?>
    <div class="dashboard-body">
        <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                    <li><span class="text-main-600 fw-normal text-15">Files Upload</span></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom border-gray-100">
                <div class="flex-between flex-wrap gap-8">
                    <div class="w-350 d-sm-block d-none">
                        <?php if (isset($error)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-danger-50 text-danger-600 mb-16'>" . safe_output($error) . "</p>"; } ?>
                        <?php if (isset($success)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-success-50 text-success-600 mb-16'>" . safe_output($success) . "</p>"; } ?>
                    </div>
                    <div class="flex-align gap-8 flex-wrap">
                        <form id="uploadForm" method="post" enctype="multipart/form-data">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <label for="fileToUpload" class="btn btn-main text-sm btn-sm px-24 py-12 d-flex align-items-center gap-8">
                                <i class="ph ph-upload-simple d-flex text-xl"></i>
                                Upload File
                            </label>
                            <input type="file" id="fileToUpload" name="fileToUpload" hidden required>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="list-view">
                    <div class="card-body p-0 overflow-x-auto scroll-sm scroll-sm-horizontal">
                        <table id="studentTable" class="table table-striped style-three w-100">
                            <thead>
                                <tr>
                                    <th class="h6 text-gray-300">File Name</th>
                                    <th class="h6 text-gray-300">Size (KB)</th>
                                    <th class="h6 text-gray-300">Uploaded Date</th>
                                    <th class="h6 text-gray-300">Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($uploadedFiles)): ?>
                                    <?php foreach ($uploadedFiles as $file): ?>
                                        <tr>
                                            <td>
                                                <span class="h6 mb-0 fw-medium text-gray-300"><?php echo safe_output($file); ?></span>
                                            </td>
                                            <td>
                                                <span class="h6 mb-0 fw-medium text-gray-300"><?php echo round(filesize($targetDir . $file) / 1024, 2); ?> KB</span>
                                            </td>
                                            <td>
                                                <span class="h6 mb-0 fw-medium text-gray-300"><?php echo date("Y-m-d H:i:s", filemtime($targetDir . $file)); ?></span>
                                            </td>
                                            <td>
                                                <a href="<?php echo safe_output($targetDir . $file); ?>" class="btn btn-sm btn-secondary" download>Download</a>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="file" value="<?php echo safe_output($file); ?>">
                                                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No files uploaded yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-footer">
        <div class="flex-between flex-wrap gap-16">
            <p class="text-gray-300 text-13 fw-normal"> &copy; Copyright Jito 2024, All Right Reserved</p>
        </div>
    </div>
</div>

<!-- Jquery js -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<!-- Bootstrap Bundle Js -->
<script src="assets/js/boostrap.bundle.min.js"></script>
    <!-- Phosphor Js -->
    <script src="assets/js/phosphor-icon.js"></script>
<!-- dataTables -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<!-- Main js -->
<script src="assets/js/main.js"></script>
<script>
    new DataTable('#studentTable', {
        searching: false,         // Disable search bar
        lengthChange: true,       // Enable changing the number of records per page
        info: true,               // Show bottom-left information (Showing 1 to 10 of X entries)
        paging: true,             // Enable pagination
        pageLength: 10,           // Default number of rows per page
        "columnDefs": [
            { "orderable": false, "targets": [0] } // Disable sorting on specific columns (like column 0)
        ],
    });

    // Get the file input and form
    const fileInput = document.getElementById('fileToUpload');
    const form = document.getElementById('uploadForm');

    // When a file is selected, submit the form automatically
    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            form.submit();
        }
    });
</script>
</body>
</html>
