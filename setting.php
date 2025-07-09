<?php
session_start(); // Start the session to store session variables
require 'config.php'; // Database connection
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


$alert_message = ''; // To store alert message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the form data
    $nama = $_POST['nama'];
    $email = $_POST['surel']; // Capture email field
    $password = $_POST['password'];
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

    // Sanitize the input data to prevent SQL injection
    $nama = mysqli_real_escape_string($conn, $nama);
    $email = mysqli_real_escape_string($conn, $email); // Ensure email is sanitized

    // Fetch user data from the database
    $query = "SELECT * FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Check if email is changed
    if ($email !== $user['email']) {

        $update_query = "UPDATE users SET email = '$email' WHERE id = '$user_id'";
        $update_result = mysqli_query($conn, $update_query);
    } else {
        // If the email is not changed, update the other fields (name, password)
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET nama = '$nama', password = '$hashed_password' WHERE id = '$user_id'";
            $update_result = mysqli_query($conn, $update_query);
        } else {
            $update_query = "UPDATE users SET nama = '$nama' WHERE id = '$user_id'";
            $update_result = mysqli_query($conn, $update_query);
        }

        $alert_message = '<div class="alert alert-success" role="alert">Profile updated successfully.</div>';
    }
}

?>

<?php

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    require 'config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accountActivation']) && $_POST['accountActivation'] == 'on') {
        $conn->begin_transaction();

        try {
            $delete_links_query = "DELETE FROM short_links WHERE user_id = '$user_id'";
            $delete_links_result = mysqli_query($conn, $delete_links_query);

            $delete_user_query = "DELETE FROM users WHERE id = '$user_id'";
            $delete_user_result = mysqli_query($conn, $delete_user_query);

            $conn->commit();

            session_destroy();

            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='alert alert-danger'>Error deleting account. Please try again later.</div>";
        }
    }
} else {
    header("Location: login.php");
    exit();
}
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

    <title>Account settings | Shortener Link</title>

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
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">Profile Details</h5>
                                    <?= $alert_message; ?>
                                    <div class="card-body">
                                        <form method="POST" id="formAccountSettings">
                                            <div class="row">
                                                <div class="mb-3 col-md-12">
                                                    <label for="nama" class="form-label">Full Name</label>
                                                    <input class="form-control" type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" autofocus required />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="surel" class="form-label">E-mail</label>
                                                    <input class="form-control" type="email" id="surel" name="surel" value="<?= htmlspecialchars($user['email']); ?>" placeholder="john.doe@example.com" required />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="password" class="form-label">New Password (Optional)</label>
                                                    <input class="form-control" type="password" id="password" name="password" placeholder="Enter new password" />
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Delete Account Section -->
                                <div class="card">
                                    <h5 class="card-header">Delete Account</h5>
                                    <div class="card-body">
                                        <div class="mb-3 col-12 mb-0">
                                            <div class="alert alert-warning">
                                                <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                                                <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                                            </div>
                                        </div>
                                        <form id="formAccountDeactivation" method="POST" action="">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" required />
                                                <label class="form-check-label" for="accountActivation">I confirm my account deactivation</label>
                                            </div>
                                            <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
                                        </form>
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

    <!-- Main JS -->
    <script src="assets_sneat/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets_sneat/js/pages-account-settings-account.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>