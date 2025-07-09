<?php
session_start(); // Start the session

// Check if session is set (for regular login)
if (!isset($_SESSION['username'])) {
    // If session is not set, check if the remember me cookie is set
    if (isset($_COOKIE['user_id'])) {
        // Find the user based on the user_id cookie value
        require 'config.php';
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $_COOKIE['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['username'] = $user['username']; // Store the username in session
        }
    } else {
        // If no session or cookie, redirect to login page
        header("Location: login.php");
        exit();
    }
}


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Database connection
    require 'config.php'; // Include your database connection file

    // Query to count the number of links for the user
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_links FROM short_links WHERE user_id = ?");
    $stmt->bind_param("i", $user_id); // Binding user ID to the query
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $total_links = $data['total_links']; // Get the total number of links

    // Query to sum the visitor count for the user's links
    $stmt = $conn->prepare("SELECT SUM(visitor_count) AS total_visitors FROM short_links WHERE user_id = ?");
    $stmt->bind_param("i", $user_id); // Binding user ID to the query
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $total_visitors = $data['total_visitors']; // Get the total number of visitors

    // Optionally, if no links or visitors exist, set to 0
    if ($total_links == 0) {
        $total_links = 0; // Ensure 0 links if no links exist
    }
    if ($total_visitors == null) {
        $total_visitors = 0; // Ensure 0 visitors if no visitors exist
    }
} else {
    $total_links = 0; // If user is not logged in, show 0 links
    $total_visitors = 0; // If user is not logged in, show 0 visitors
}
// If user is logged in (either by session or cookie), proceed to the dashboard
?>

<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="assets_sneat/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard | Shortener Link</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets_sneat/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets_sneat/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets_sneat/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets_sneat/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets_sneat/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets_sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="assets_sneat/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets_sneat/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets_sneat/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include 'template/sidebar.php'  ?>

            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include 'template/navbar.php'  ?>


                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-lg-8 mb-4 order-0">
                                <div class="card">
                                    <div class="d-flex align-items-end row">
                                        <div class="col-sm-7">
                                            <div class="card-body">
                                                <h5 class="card-title text-primary">Welcome <?php echo $user_nama; ?>! 🎉</h5>
                                                <p class="mb-4">
                                                    All the information you need to know about your account is here.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 text-center text-sm-left">
                                            <div class="card-body pb-0 px-0 px-md-4">
                                                <img
                                                    src="assets_sneat/img/illustrations/man-with-laptop-light.png"
                                                    height="140"
                                                    alt="View Badge User"
                                                    data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                                    data-app-light-img="illustrations/man-with-laptop-light.png" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 order-1">
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <!-- <img
                                                            src="assets_sneat/img/icons/unicons/chart-success.png"
                                                            alt="chart success"
                                                            class="rounded" /> -->
                                                        <i class="bx bx-link"></i>
                                                    </div>
                                                </div>
                                                <span class="fw-semibold d-block mb-1">Link</span>
                                                <h3 class="card-title mb-2"><?php echo number_format($total_links); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <!-- <img
                                                            src="assets_sneat/img/icons/unicons/wallet-info.png"
                                                            alt="Credit Card"
                                                            class="rounded" /> -->
                                                        <i class="bx bx-stats"></i>
                                                    </div>

                                                </div>
                                                <span class="fw-semibold d-block mb-1">Visitor</span>
                                                <h3 class="card-title text-nowrap mb-1"><?php echo number_format($total_visitors); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include 'template/footer.php'  ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets_sneat/vendor/libs/jquery/jquery.js"></script>
    <script src="assets_sneat/vendor/libs/popper/popper.js"></script>
    <script src="assets_sneat/vendor/js/bootstrap.js"></script>
    <script src="assets_sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets_sneat/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="assets_sneat/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="assets_sneat/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets_sneat/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>