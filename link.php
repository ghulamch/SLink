<?php
session_start();
require 'config.php'; // Include your database connection file

$alert_message = ''; // To store alert messages
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



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $original_url = $_POST['original_url'];
    $custom_short_link = $_POST['custom_short_link'];
    $password = $_POST['password'];
    $judul = $_POST['judul'];
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

    // Check if the user provided a custom short link
    if (empty($custom_short_link)) {
        // Generate a random 6-character short link if not provided
        $short_link = generateRandomString(6);
    } else {
        $short_link = $custom_short_link;
    }

    // Check if the short link already exists using direct SQL query
    $query = "SELECT * FROM short_links WHERE short_link = '$short_link'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $alert_message = '<div class="alert alert-danger" role="alert">Short link already exists. Please choose another one.</div>';
    } else {
        // Hash the password if provided
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $hashed_password = null;
        }

        // Insert the new short link into the database using direct SQL query
        $query = "INSERT INTO short_links (original_url, short_link, user_id, judul, password) 
                  VALUES ('$original_url', '$short_link', '$user_id', '$judul', '$hashed_password')";

        if (mysqli_query($conn, $query)) {
            $alert_message = '<div class="alert alert-success" role="alert">Short link created successfully! Your link: <a href="redirect.php?short_link=' . $short_link . '">/' . $short_link . '</a></div>';
        } else {
            $alert_message = '<div class="alert alert-danger" role="alert">Error: Could not create short link!</div>';
        }
    }
}

// Function to generate a random string
function generateRandomString($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>


<?php
require 'config.php'; // Include database connection

// Assuming user_id is stored in the session after login
$user_id = $_SESSION['user_id'];

// Query to fetch data based on the user_id using direct SQL query
$query = "SELECT * FROM short_links WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

// Check if data exists
if (mysqli_num_rows($result) > 0) {
    $shortLinks = mysqli_fetch_all($result, MYSQLI_ASSOC); // Fetch all rows as an associative array
} else {
    $shortLinks = [];
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

    <title>List Link | Shortener Link</title>

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
                        <div class="col-lg-4 col-md-6">
                            <div class="mt-3">
                                <!-- Button trigger modal -->
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addModal">
                                    Add Link
                                </button>


                            </div>
                        </div>
                        <hr class="my-3" />
                        <!-- Responsive Table -->
                        <div class="card">
                            <h5 class="card-header">Short Link</h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr class="text-nowrap">
                                            <th>#</th>
                                            <th>Original URL</th>
                                            <th>Custom Short Link</th>
                                            <th>Title</th>
                                            <th>Visitor Count</th>
                                            <th>Password Protected</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        if (!empty($shortLinks)):
                                        ?>
                                            <?php foreach ($shortLinks as $link): ?>
                                                <tr>
                                                    <th scope="row"><?= $no++; ?></th>
                                                    <td><?= htmlspecialchars(strlen($link['original_url']) > 50 ? substr($link['original_url'], 0, 50) . '...' : $link['original_url']); ?></td>
                                                    <td>s.ghulam.my.id/<?= htmlspecialchars($link['short_link']); ?></td>
                                                    <td><?= htmlspecialchars($link['judul']) ? htmlspecialchars($link['judul']) : ''; ?></td>
                                                    <td id="visitor_count_<?= $link['id']; ?>"><?= htmlspecialchars($link['visitor_count']); ?></td>
                                                    <td><?= $link['password'] ? 'Yes' : 'No'; ?></td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm" onclick="copyToClipboard('<?= 's.ghulam.my.id/' . htmlspecialchars($link['short_link']); ?>')">Copy</button>
                                                        <a href="delete.php?id=<?= $link['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this link?')">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No short links found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <!--/ Responsive Table -->
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
    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Link</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="original_url" class="form-label">Original URL</label>
                                <input type="url" name="original_url" id="original_url" class="form-control" placeholder="Enter Original URL" require />
                            </div>
                        </div>
                        <div class="row g-2">
                            <label class="form-label">Custom Short Link (optional)</label>
                            <div class="col mb-0">
                                <input type="text" id="emailBasic" class="form-control" placeholder="s.ghulam.my.id/" disabled />
                            </div>
                            <div class="col mb-0">
                                <input type="text" id="custom_short_link" name="custom_short_link" class="form-control" placeholder="" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="judul" class="form-label">Judul (Optional)</label>
                                <input type="text" name="judul" id="judul" class="form-control" placeholder="Enter Judul" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="password" class="form-label">Password (Optional)</label>
                                <input type="text" name="password" id="password" class="form-control" placeholder="Enter password" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Toast notification -->
    <div id="toast" class="toast" style="position: fixed; bottom: 20px; right: 20px; display: none; background-color: #28a745; color: white; padding: 10px; border-radius: 5px;">
        Copied to clipboard!
    </div>

    <script>
        function copyToClipboard(link) {
            // Create a temporary input element to hold the link
            const tempInput = document.createElement("input");
            document.body.appendChild(tempInput);
            tempInput.value = link; // Set the value to the link we want to copy
            tempInput.select();
            document.execCommand("copy"); // Execute the copy command

            // Remove the temporary input element
            document.body.removeChild(tempInput);

            // Show toast notification
            const toast = document.getElementById("toast");
            toast.style.display = "block";

            // Hide the toast after 3 seconds
            setTimeout(() => {
                toast.style.display = "none";
            }, 3000);
        }
    </script>


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js 
        -->
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

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>