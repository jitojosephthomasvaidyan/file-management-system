<?php
session_start();
include 'includes/db.php';  // Ensure this includes the database connection ($conn)

// Generate a CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Sanitize and validate input
    $cname = filter_var($_POST['cname'], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);  // assuming email as username
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if username exists using prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if (mysqli_num_rows($result) > 0) {
        $error = "Username already exists.";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (cname, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $cname, $username, $password);
        if ($stmt->execute()) {
            // Create a directory with the username in the uploads folder
            $userDir = 'uploads/' . $username;
            if (!is_dir($userDir)) {
                mkdir($userDir, 0777, true); // Create the directory with read/write/execute permissions
            }

            // Set session and redirect
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');  // Redirect to the dashboard after registration
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>File Management System - Sign Up</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/logo.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
     <!-- file upload -->
     <link rel="stylesheet" href="assets/css/file-upload.css">
    <!-- file upload -->
    <link rel="stylesheet" href="assets/css/plyr.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <!-- full calendar -->
    <link rel="stylesheet" href="assets/css/full-calendar.css">
    <!-- jquery Ui -->
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <!-- editor quill Ui -->
    <link rel="stylesheet" href="assets/css/editor-quill.css">
    <!-- apex charts Css -->
    <link rel="stylesheet" href="assets/css/apexcharts.css">
    <!-- calendar Css -->
    <link rel="stylesheet" href="assets/css/calendar.css">
    <!-- jvector map Css -->
    <link rel="stylesheet" href="assets/css/jquery-jvectormap-2.0.5.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    
<!--==================== Preloader Start ====================-->
  <div class="preloader">
    <div class="loader"></div>
  </div>
<!--==================== Preloader End ====================-->

<!--==================== Sidebar Overlay End ====================-->
<div class="side-overlay"></div>
<!--==================== Sidebar Overlay End ====================-->

<section class="auth d-flex">
    <div class="auth-left bg-main-50 flex-center p-24">
        <img src="assets/images/auth-img1.png" alt="Auth Image">
    </div>
    <div class="auth-right py-40 px-24 flex-center flex-column">
        <div class="auth-right__inner mx-auto w-100">
            <a href="#" class="auth-right__logo w-100 mb-10">
                <img src="assets/images/logo.png" alt="Logo"><span>File Manage System</span>
            </a>
            <h2 class="mb-8">Sign Up</h2>
            <p class="text-gray-600 text-15 mb-32">Please sign up for your account and start the adventure.</p>
            
            <!-- Display any errors securely -->
            <?php if (isset($error)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-danger-50 text-danger-600 mb-16'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>

            <form method="post">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-24">
                    <label for="cname" class="form-label mb-8 h6">Name</label>
                    <div class="position-relative">
                        <input type="text" name="cname" class="form-control py-11 ps-40" id="cname" placeholder="Type your Name" required>
                        <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                    </div>
                </div>
                <div class="mb-24">
                    <label for="email" class="form-label mb-8 h6">Email</label>
                    <div class="position-relative">
                        <input type="email" name="username" class="form-control py-11 ps-40" id="email" placeholder="Type your email address" required>
                        <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-envelope"></i></span>
                    </div>
                </div>
                <div class="mb-24">
                    <label for="current-password" class="form-label mb-8 h6">Password</label>
                    <div class="position-relative">
                        <input name="password" type="password" class="form-control py-11 ps-40" id="current-password" placeholder="Enter Password" required>
                        <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash"></span>
                        <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                    </div>
                </div>

                <button type="submit" class="btn btn-main rounded-pill w-100">Sign Up</button>
                <p class="mt-32 text-gray-600 text-center">Already have an account?
                    <a href="index.php" class="text-main-600 hover-text-decoration-underline">Log In</a>
                </p>
            </form>
        </div>
    </div>
</section>

    <!-- Jquery js -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="assets/js/boostrap.bundle.min.js"></script>
    <!-- Phosphor Js -->
    <script src="assets/js/phosphor-icon.js"></script>
    <!-- file upload -->
    <script src="assets/js/file-upload.js"></script>
    <!-- file upload -->
    <script src="assets/js/plyr.js"></script>
    <!-- dataTables -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <!-- full calendar -->
    <script src="assets/js/full-calendar.js"></script>
    <!-- jQuery UI -->
    <script src="assets/js/jquery-ui.js"></script>
    <!-- jQuery UI -->
    <script src="assets/js/editor-quill.js"></script>
    <!-- apex charts -->
    <script src="assets/js/apexcharts.min.js"></script>
    <!-- Calendar Js -->
    <script src="assets/js/calendar.js"></script>
    <!-- jvectormap Js -->
    <script src="assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <!-- jvectormap world Js -->
    <script src="assets/js/jquery-jvectormap-world-mill-en.js"></script>

</body>
</html>
