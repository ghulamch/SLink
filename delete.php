<?php
session_start(); // Start the session

require 'config.php'; // Database connection

// Check if the user is logged in (make sure session has user_id)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Check if 'id' is provided via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // The logged-in user

    // Check if the record exists and belongs to the logged-in user
    $stmt = $conn->prepare("SELECT * FROM short_links WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // The link exists and belongs to the logged-in user, proceed with deletion
        $delete_stmt = $conn->prepare("DELETE FROM short_links WHERE id = ?");
        $delete_stmt->bind_param("i", $id);

        if ($delete_stmt->execute()) {
            // Redirect to the page displaying the table after deletion
            header("Location: link.php");
            exit();
        } else {
            echo "Error deleting record.";
        }
    } else {
        // Record doesn't exist or doesn't belong to the logged-in user
        echo "You cannot delete this record.";
    }
} else {
    // If no ID is provided, show an error
    echo "No ID provided for deletion.";
}
