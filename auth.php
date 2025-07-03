<?php
header("Content-Type: application/json");
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "attendance_system");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Get JSON input from JavaScript
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data["email"] ?? '');
$password = trim($data["password"] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Username and password required."]);
    exit;
}

// Check user in database
$stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {
    // Use "username" for session, as required by dashboard.php
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["email"];
    $_SESSION["role"] = $user["role"];
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid email or password."]);
}
?>
