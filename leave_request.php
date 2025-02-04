<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Form</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4b5563;
            font-weight: 500;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        button:hover {
            background: #1d4ed8;
        }

        #totalDays {
            margin-top: 0.5rem;
            font-weight: 500;
            color: #2563eb;
        }

        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Leave Request Form</h1>
        </div>

        <form id="leaveRequestForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="employeeName">Full Name</label>
                    <input type="text" id="employeeName" name="employeeName" required>
                </div>
                <div class="form-group">
                    <label for="employeeEmail">Email</label>
                    <input type="email" id="employeeEmail" name="employeeEmail" required>
                </div>
            </div>
            <div class="form-group">
                <label for="leaveType">Leave Type</label>
                <select id="leaveType" name="leaveType" required>
                    <option value="">Select Leave Type</option>
                    <option value="Annual">Annual Leave</option>
                    <option value="Sick">Sick Leave</option>
                    <option value="Personal">Personal Leave</option>
                    <option value="Emergency">Emergency Leave</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="startDate">Start Date</label>
                    <input type="date" id="startDate" name="startDate" required>
                </div>
                <div class="form-group">
                    <label for="endDate">End Date</label>
                    <input type="date" id="endDate" name="endDate" required>
                </div>
            </div>

            <div class="form-group">
                <div id="totalDays"></div>
            </div>

            <div class="form-group">
                <label for="reason">Reason for Leave</label>
                <textarea id="reason" name="reason" rows="4" required></textarea>
            </div>

            <button type="submit">Submit Leave Request</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            // Set minimum date as today for both date inputs
            const today = new Date().toISOString().split('T')[0];
            $('#startDate, #endDate').attr('min', today);

            // Calculate total days when dates change
            function calculateDays() {
                const startDate = new Date($('#startDate').val());
                const endDate = new Date($('#endDate').val());

                if (startDate && endDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    $('#totalDays').text(`Total Days: ${diffDays}`);
                    return diffDays;
                }
                return 0;
            }

            $('#startDate, #endDate').on('change', calculateDays);

            // Handle form submission
            $('#leaveRequestForm').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                form.addClass('loading');

                // Get form data
                const formData = new FormData();
                formData.append('employeeName', $('#employeeName').val());
                formData.append('employeeEmail', $('#employeeEmail').val());
                formData.append('leaveType', $('#leaveType').val());
                formData.append('startDate', $('#startDate').val());
                formData.append('endDate', $('#endDate').val());
                formData.append('totalDays', calculateDays());
                formData.append('reason', $('#reason').val());

                // Submit form using AJAX
                $.ajax({
                    url: 'submit_leave.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        form.removeClass('loading');

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonColor: '#2563eb'
                            }).then(() => {
                                form[0].reset();
                                $('#totalDays').text('');
                                window.location.href = './index.php?page=view_leaves';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Something went wrong',
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        form.removeClass('loading');
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonColor: '#2563eb'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>