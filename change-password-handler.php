<!-- dashboard.php -->
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "attendance_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];
$oldPassword = trim($_POST['old_password']);
$newPassword = trim($_POST['new_password']);
$confirmPassword = trim($_POST['confirm_password']);

if ($newPassword !== $confirmPassword) {
    header("Location: change-password.php?error=New passwords do not match");
    exit;
}

// Fetch current password from database
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($oldPassword, $user['password'])) {
    header("Location: change-password.php?error=Current password is incorrect");
    exit;
}

// Hash and update new password
$newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$updateStmt->bind_param("si", $newHashedPassword, $userId);
$updateStmt->execute();

header("Location: change-password.php?success=1");
exit;
?>
