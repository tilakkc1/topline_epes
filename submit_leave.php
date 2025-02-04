<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and sanitize inputs
        $employeeName = filter_var($_POST['employeeName'], FILTER_SANITIZE_STRING);
        $employeeEmail = filter_var($_POST['employeeEmail'], FILTER_SANITIZE_EMAIL);
        $leaveType = filter_var($_POST['leaveType'], FILTER_SANITIZE_STRING);
        $startDate = filter_var($_POST['startDate'], FILTER_SANITIZE_STRING);
        $endDate = filter_var($_POST['endDate'], FILTER_SANITIZE_STRING);
        $totalDays = filter_var($_POST['totalDays'], FILTER_VALIDATE_INT);
        $reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);

        // Validate required fields
        if (
            empty($employeeName) || empty($employeeEmail) || empty($leaveType) ||
            empty($startDate) || empty($endDate) || empty($reason)
        ) {
            throw new Exception('All fields are required');
        }

        // Validate email
        if (!filter_var($employeeEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }

        // Insert into database
        $query = "INSERT INTO leave_requests (employee_name, employee_email, leave_type, start_date, end_date, total_days, reason, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param(
            "sssssss",
            $employeeName,
            $employeeEmail,
            $leaveType,
            $startDate,
            $endDate,
            $totalDays,
            $reason
        );

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Your leave request has been submitted successfully!'
            ]);
        } else {
            throw new Exception("Error executing statement: " . $stmt->error);
        }

    } catch (Exception $e) {
        error_log("Leave Request Error: " . $e->getMessage());
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