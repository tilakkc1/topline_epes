<?php
include 'db_connect.php';

// Get all leave requests with latest first
$query = "SELECT * FROM leave_requests ORDER BY applied_on DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Requests - Sereno System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: #f9fafb;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .new-request-btn {
            background: white;
            color: #2563eb;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .new-request-btn:hover {
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .leave-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .leave-table th,
        .leave-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .leave-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #4b5563;
        }

        .leave-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            font-weight: 500;
        }

        .approve-btn {
            background: #dcfce7;
            color: #166534;
        }

        .reject-btn {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Leave Requests</h1>
            <?php if ($_SESSION['login_type'] != 2): ?>
                <a href="./index.php?page=leave_request" class="new-request-btn">New Request</a>
            <?php endif; ?>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table class="leave-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Total Days</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <?php if ($_SESSION['login_type'] == 2): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['employee_name']); ?><br>
                                <small style="color: #6b7280;"><?php echo htmlspecialchars($row['employee_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                            <td>
                                <?php
                                echo date('M d, Y', strtotime($row['start_date']));
                                if ($row['start_date'] !== $row['end_date']) {
                                    echo ' - ' . date('M d, Y', strtotime($row['end_date']));
                                }
                                ?>
                            </td>
                            <td><?php echo $row['total_days']; ?> days</td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($row['applied_on'])); ?></td>
                            <?php if ($_SESSION['login_type'] == 2): ?>
                                <td class="actions">
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <button class="action-btn approve-btn"
                                            onclick="updateStatus(<?php echo $row['id']; ?>, 'Approved')">
                                            Approve
                                        </button>
                                        <button class="action-btn reject-btn"
                                            onclick="updateStatus(<?php echo $row['id']; ?>, 'Rejected')">
                                            Reject
                                        </button>
                                    <?php else: ?>
                                        <span class="status-badge">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No leave requests found.</p>
                <?php if ($_SESSION['login_type'] != 2): ?>
                    <a href="leave_request.php" class="new-request-btn">Create New Request</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateStatus(id, status) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${status.toLowerCase()} this leave request?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'update_leave_status.php',
                        type: 'POST',
                        data: {
                            id: id,
                            status: status
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: response.message,
                                    confirmButtonColor: '#2563eb'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                    confirmButtonColor: '#2563eb'
                                });
                            }
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>