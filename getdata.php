<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Submissions</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .header, .footer {
            background-color: #004080;
            color: white;
            padding: 10px 0;
        }
        .header h1, .footer p {
            margin: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Emmanuel Alayande University of Education</h1>
    </div>
    <div class="container">
        <h2 class="text-center mt-4">Submitted Information</h2>
        <table id="submissionsTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Staff Number</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Uploaded File</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted here by the PHP script -->
                <?php
                $servername = "your_server_name";
                $username = "your_username";
                $password = "your_password";
                $dbname = "your_database_name";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT audit_id, staff_name, staff_no, staff_email, staff_phone, staff_file FROM audit_file";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["staff_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["staff_no"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["staff_email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["staff_phone"]) . "</td>";
                        echo "<td><a href='" . htmlspecialchars($row["staff_file"]) . "' target='_blank'>View</a></td>";
                        echo "<td><button class='btn btn-danger delete-btn' data-id='" . $row["audit_id"] . "' data-file='" . $row["staff_file"] . "'>Delete</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No submissions found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <div class="footer mt-5">
        <p>&copy; 2024 Emmanuel Alayande University of Education</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            $('#submissionsTable').DataTable();

            // Handle delete button click
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');
                const file = $(this).data('file');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete.php',
                            type: 'POST',
                            data: { id: id, file: file },
                            success: function(response) {
                                const data = JSON.parse(response);
                                if (data.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        data.message,
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
