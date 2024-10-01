<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>File Management System</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/logo.png">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<?php
session_start();
include 'includes/db.php'; // Ensure this contains the connection ($conn) to the database

// Generate a CSRF token if not already generated
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Sanitize and validate input
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['username'] = $username;
        header('Location: dashboard.php');  // Redirect to dashboard
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?> 
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
            <img src="assets/images/auth-img1.png" alt="">
        </div>
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <a href="" class="auth-right__logo w-100 mb-10">
                <img src="assets/images/logo.png" alt="Logo"><span>File Manage System</span>
                </a>
                <h2 class="mb-8">Welcome Back! &#128075;</h2>
                <p class="text-gray-600 text-15 mb-32">Please sign in to your account and start the adventure</p>

                <!-- Display error messages securely -->
                <?php if (isset($error)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-danger-50 text-danger-600 mb-16'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>

                <form method="POST">
                    <!-- Include CSRF token for form protection -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-24">
                        <label for="fname" class="form-label mb-8 h6">Email or Username</label>
                        <div class="position-relative">
                            <input type="text" name="username" class="form-control py-11 ps-40" id="fname" placeholder="Type your username" required>
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                        </div>
                    </div>
                    <div class="mb-24">
                        <label for="current-password" class="form-label mb-8 h6">Current Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control py-11 ps-40" id="current-password" placeholder="Enter Current Password" required>
                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="#current-password"></span>
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-main rounded-pill w-100">Sign In</button>
                    <p class="mt-32 text-gray-600 text-center">New on our platform?
                        <a href="sign-up.php" class="text-main-600 hover-text-decoration-underline">Create an account</a>
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
<!-- Main js -->
<script src="assets/js/main.js"></script>

</body>
</html>
