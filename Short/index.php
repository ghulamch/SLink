<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'slink';

// Create a connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
$alert_message = '';
// Check if a short link is provided via URL (using the path without 'redirect.php')
if (isset($_GET['short_link'])) {
    $short_link = $_GET['short_link'];
    $alert_message = ''; // To store alert messages

    // Fetch the short link details from the database
    $stmt = $conn->prepare("SELECT * FROM short_links WHERE short_link = ?");
    $stmt->bind_param("s", $short_link);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $link_data = $result->fetch_assoc();

        // Check if password is set and validate it
        if ($link_data['password']) {
            if (isset($_POST['password'])) {
                // Check if password matches
                if (password_verify($_POST['password'], $link_data['password'])) {
                    // Increment visitor count
                    incrementVisitorCount($short_link);
                    header("Location: " . $link_data['original_url']);
                    exit();
                } else {
                    $alert_message = '<div class="alert alert-danger" role="alert">Incorrect password!</div>';
                }
            }
            // Show password form
            echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>Enter Password</title>

        <!-- Sneat Template CSS -->
        <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="assets/css/demo.css" />

        <!-- Bootstrap & Icons -->
        <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

        <script src="assets/vendor/js/helpers.js"></script>
    </head>

    <body>
        <div class="container-xxl">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-4">
                    <!-- Card -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="app-brand justify-content-center">
                                <a href="index.html" class="app-brand-link gap-2">
                                    <span class="app-brand-logo demo">
                                        <svg
                                            width="25"
                                            viewBox="0 0 25 42"
                                            version="1.1"
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                        >
                                            <defs>
                                                <path
                                                    d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                                                    id="path-1"
                                                ></path>
                                            </defs>
                                            <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                                    <g id="Icon" transform="translate(27.000000, 15.000000)">
                                                        <g id="Mask" transform="translate(0.000000, 8.000000)">
                                                            <mask id="mask-2" fill="white">
                                                                <use xlink:href="#path-1"></use>
                                                            </mask>
                                                            <use fill="#696cff" xlink:href="#path-1"></use>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="app-brand-text demo text-body fw-bolder">SLINK</span>
                                </a>
                            </div>
                            <!-- /Logo -->

                            <h4 class="mb-2">Enter Password 🔒</h4>
                            <p class="mb-4">Please enter the password to access the original link:</p>';

            // Display alert if password is incorrect
            if (isset($alert_message)) {
                echo $alert_message;
            }

            echo '
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" required />
                                </div>
                                <button type="submit" class="btn btn-primary d-grid w-100">Submit</button>
                            </form>
                        </div>
                    </div>
                    <!-- /Card -->
                </div>
            </div>
        </div>
    </body>
    </html>';
        } else {
            // No password protection, just increment and redirect
            incrementVisitorCount($short_link);
            header("Location: " . $link_data['original_url']);
            exit();
        }
    } else {
        header("Location: error.html");
        exit();
    }
}

function incrementVisitorCount($short_link)
{
    global $conn;

    // Increment the visitor count
    $stmt = $conn->prepare("UPDATE short_links SET visitor_count = visitor_count + 1 WHERE short_link = ?");
    $stmt->bind_param("s", $short_link);
    $stmt->execute();
}
