<?php 
include('layout/header.php');
include('layout/asidebar.php');


$username = $_SESSION['username'];

// Prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM file_shares WHERE shared_with = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result1 = $stmt->get_result();

?>

<div class="dashboard-main-wrapper">
    <?php include('layout/top-bar.php'); ?>
    <div class="dashboard-body">
        <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
            <!-- Breadcrumb Start -->
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-normal text-15">Shared Files With You</span></li>
                </ul>
            </div>
        </div>

        <!-- Card Start -->
        <div class="card">
            <!-- Card Header Start -->
            <div class="card-header border-bottom border-gray-100">
                <div class="flex-between flex-wrap gap-8">
                    <div class="w-350 d-sm-block d-none">
                        <?php if (isset($error)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-danger-50 text-danger-600 mb-16'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>
                        <?php if (isset($success)) { echo "<p class='text-13 py-2 px-10 rounded-pill bg-success-50 text-success-600 mb-16'>" . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . "</p>"; } ?>
                    </div>
                </div>
            </div>
            <!-- Card Header End -->

            <!-- Card Body Start -->
            <div class="card-body p-0">
                <div class="list-view">
                    <div class="card-body p-0 overflow-x-auto scroll-sm scroll-sm-horizontal">
                        <table id="studentTable" class="table table-striped style-three w-100">
                            <thead>
                                <tr>
                                    <th class="h6 text-gray-300">File Name</th>
                                    <th class="h6 text-gray-300">Shared By</th>
                                    <th class="h6 text-gray-300">Shared Date</th>
                                    <th class="h6 text-gray-300">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result1->num_rows > 0): ?>
                                    <?php while ($row = $result1->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($row['file_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td>
                                                <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($row['owner'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td>
                                                <span class="h6 mb-0 fw-medium text-gray-300"><?php echo date("Y-m-d H:i:s", strtotime($row['created_at'])); ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $filePath = "uploads/" . htmlspecialchars($row['owner'], ENT_QUOTES, 'UTF-8') . "/" . htmlspecialchars($row['file_name'], ENT_QUOTES, 'UTF-8');
                                                if (file_exists($filePath)): ?>
                                                    <a href="<?php echo $filePath; ?>" class="btn btn-sm btn-secondary" download>Download</a>
                                                <?php else: ?>
                                                    <span class="text-danger">File not found</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No files shared with you yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Card Body End -->
        </div>
        <!-- Card End -->
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
<!-- DataTables -->
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
</script>
</body>
</html>
