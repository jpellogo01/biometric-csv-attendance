<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'upload-form', 'report-summary', 'change-password', 'view-working-hours'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// DB Connection (you already have this)
$host = "localhost";
$username = "root";
$password = "";
$database = "attendance_system";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<?php if (!isset($conn)) {
    die("No DB connection");
} ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Attendance System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
}
</style>

<body class="bg-light vh-100 d-flex flex-column">
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid flex-grow-1 overflow-hidden">
        <div class="row h-100">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 bg-dark text-white p-0 overflow-auto" style="max-height: calc(100vh - 56px);">
                <?php include 'includes/sidebar.php'; ?>
            </div>

            <!-- Main content area -->
            <div class="col-md-9 col-lg-10 p-4 bg-white overflow-auto" style="max-height: calc(100vh - 56px);">
                <?php include 'pages/' . $page . '.php'; ?>
            </div>
        </div>
    </div>
</body>



</html>