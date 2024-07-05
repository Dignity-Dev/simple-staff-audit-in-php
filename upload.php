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

// Check if staff already exists
$staff_no = $_POST['staff_no'];
$sql = "SELECT * FROM audit_file WHERE staff_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Staff already exists
    echo json_encode([
        "success" => false,
        "message" => "Staff member already exists."
    ]);
    exit();
}

// File upload handling
$target_dir = "uploads/";
$target_file = time()." - ". basename($_FILES["file"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if file is a PDF
if ($fileType != "pdf") {
    echo json_encode([
        "success" => false,
        "message" => "Only PDF files are allowed."
    ]);
    exit();
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    // Save to database
    $sql = "INSERT INTO audit_file (staff_name, staff_phone, staff_email, staff_file, staff_no) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $_POST['name'], $_POST['phone'], $_POST['email'], $target_file, $staff_no);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "File uploaded and data saved successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to save data to database."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to upload file."
    ]);
}

$stmt->close();
$conn->close();

