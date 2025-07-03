<?php
// Do NOT call session_start() again — it's already started in dashboard.php

// DB connection (already exists in dashboard.php too, but added here if run standalone)
$host = "localhost";
$username = "root";
$password = "";
$database = "attendance_system";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total number of employees
$totalEmployeesQuery = "SELECT COUNT(DISTINCT employee_id) AS total FROM attendance_logs";
$totalEmployeesResult = $conn->query($totalEmployeesQuery);
if (!$totalEmployeesResult) {
    die("Query Error (Total Employees): " . $conn->error);
}
$totalEmployees = $totalEmployeesResult->fetch_assoc()['total'] ?? 0;

// Get employee with highest average working hours
$topEmployeeQuery = "
    SELECT 
        name,
        AVG(daily_hours) AS avg_hours
    FROM (
        SELECT 
            employee_id,
            name,
            DATE(timestamp) AS log_date,
            TIMESTAMPDIFF(SECOND, MIN(timestamp), MAX(timestamp)) / 3600 AS daily_hours
        FROM attendance_logs
        GROUP BY employee_id, log_date
    ) AS daily_work
    GROUP BY employee_id
    ORDER BY avg_hours DESC
    LIMIT 1
";

$topEmployeeResult = $conn->query($topEmployeeQuery);
if (!$topEmployeeResult) {
    die("Query Error (Top Performer): " . $conn->error);
}
$topEmployee = $topEmployeeResult->fetch_assoc();
$topName = $topEmployee['name'] ?? 'N/A';
$topHours = isset($topEmployee['avg_hours']) ? number_format($topEmployee['avg_hours'], 2) : '0.00';
?>

<style>
    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
    }
    .stats-card {
        transition: 0.3s ease;
    }
    .stats-card:hover {
        transform: scale(1.02);
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
</style>

<div class="container">
    <div class="mb-4">
        <h2 class="fw-bold text-primary">🏠 Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <p class="lead">This is your Attendance System Dashboard. Here's an overview of your system:</p>
    </div>

    <!-- Statistics Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card stats-card border-0 shadow rounded-4 text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">👥 Total Employees</h5>
                    <h3 class="fw-bold"><?= $totalEmployees ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card stats-card border-0 shadow rounded-4 text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">🏆 Top Performer</h5>
                    <p class="mb-1 fs-5"><?= htmlspecialchars($topName) ?></p>
                    <small>Average Daily Hours: <strong><?= $topHours ?> hrs</strong></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Section -->
    <div class="card shadow rounded-4 p-4">
        <h5 class="mb-3">🔍 What You Can Do:</h5>
        <ul class="list-unstyled fs-5">
            <li>📤 Upload attendance logs easily</li>
            <li>🕒 View working hours per employee</li>
            <li>📊 Generate reports with date filters</li>
            <li>🔒 Change your password securely</li>
        </ul>
    </div>
</div>
