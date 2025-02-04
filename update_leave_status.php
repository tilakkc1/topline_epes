<?php
include 'db_connect.php';
session_start();

header('Content-Type: application/json');

// Check if user is admin (login_type == 2)
if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] != 2) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

        if (!$id) {
            throw new Exception('Invalid request ID');
        }

        if (!in_array($status, ['Approved', 'Rejected'])) {
            throw new Exception('Invalid status');
        }

        $stmt = $conn->prepare("UPDATE leave_requests SET status = ?, reviewed_by = ?, reviewed_on = NOW() WHERE id = ?");
        $reviewer = $_SESSION['login_name'] ?? 'Admin'; // Using login_name from session
        $stmt->bind_param("ssi", $status, $reviewer, $id);

        if ($stmt->execute()) {
            // Send notification or email to employee about status update
            // You can add email notification code here

            echo json_encode([
                'status' => 'success',
                'message' => 'Leave request has been ' . strtolower($status)
            ]);
        } else {
            throw new Exception('Failed to update status');
        }

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>