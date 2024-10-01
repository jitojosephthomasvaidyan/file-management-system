<?php

// Include necessary files and ensure user is authenticated
include('layout/header.php');
include('layout/asidebar.php');
include('includes/db.php');  // Assuming you have a db.php file for the DB connection

// Generate a CSRF token if it's not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$username = $_SESSION['username'];
$userDir = "uploads/$username/";

// Function to sanitize file names
function sanitizeFileName($fileName) {
    return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
}

// Sanitize any inputs that will be displayed back
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Ensure directory exists for user files
if (!is_dir($userDir)) {
    mkdir($userDir, 0777, true);
}

$files = array_diff(scandir($userDir), array('.', '..'));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Sanitize inputs
    $fileToShare = sanitizeFileName($_POST['file']);
    $sharedWith = mysqli_real_escape_string($conn, $_POST['shared_with']);

    // Check if the user to share with exists using a prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $sharedWith);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Insert share entry into the file_shares table using prepared statements
        $stmt = $conn->prepare("INSERT INTO file_shares (file_name, owner, shared_with) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fileToShare, $username, $sharedWith);
        if ($stmt->execute()) {
            $success = "File shared successfully with " . safe_output($sharedWith) . ".";
        } else {
            $error = "Error sharing the file: " . mysqli_error($conn);
        }
    } else {
        $error = "User does not exist.";
    }
}
?>

<div class="dashboard-main-wrapper">
    <?php include('layout/top-bar.php'); ?>
    <div class="dashboard-body">
        <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-normal text-15">Share Files</span></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                <h5 class="mb-0">Share The File</h5> 
                <br>
                <?php if (isset($error)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-danger-50 text-danger-600 mb-16'>" . safe_output($error) . "</p>"; } ?>
                <?php if (isset($success)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-success-50 text-success-600 mb-16'>" . safe_output($success) . "</p>"; } ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="row gy-20">
                        <div class="col-xxl-12 col-md-12 col-sm-12">
                            <div class="row g-20">
                                <div class="col-sm-6">
                                    <label class="h5 mb-8 fw-semibold font-heading" for="file">Select File to Share:</label>
                                    <div class="position-relative">
                                        <select name="file" id="file" class="form-select py-9 placeholder-13 text-15" required>
                                            <option value="" disabled selected>Select File</option>
                                            <?php foreach ($files as $file): ?>
                                                <option value="<?php echo safe_output($file); ?>"><?php echo safe_output($file); ?></option>
                                            <?php endforeach; ?>
                                        </select>                                            
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="h5 mb-8 fw-semibold font-heading" for="shared_with">Share With (username):</label>
                                    <div class="position-relative">
                                        <select name="shared_with" id="shared_with" required class="form-select py-9 placeholder-13 text-15">
                                            <option value="" disabled selected>Select User</option>
                                            <?php 
                                            // Fetch users except the current logged-in user using prepared statements
                                            $stmt = $conn->prepare("SELECT username, cname FROM users WHERE username != ?");
                                            $stmt->bind_param("s", $username);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . safe_output($row['username']) . "'>" . safe_output($row['cname']) . "</option>";
                                            }
                                            ?>
                                        </select>                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-align justify-content-end gap-8">
                            <button type="submit" class="btn btn-main rounded-pill py-9">Continue</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="dashboard-footer">
        <div class="flex-between flex-wrap gap-16">
            <p class="text-gray-300 text-13 fw-normal"> &copy; Copyright Jito 2024, All Right Reserverd</p>
        </div>
    </div>
</div>

<!-- Jquery js -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<!-- Bootstrap Bundle Js -->
<script src="assets/js/boostrap.bundle.min.js"></script>
    <!-- Phosphor Js -->
    <script src="assets/js/phosphor-icon.js"></script>
<!-- Main js -->
<script src="assets/js/main.js"></script>

</body>
</html>
