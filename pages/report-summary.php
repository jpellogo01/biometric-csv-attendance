<?php if (!isset($conn)) die("No DB connection"); ?>

<?php
$from = isset($_GET['from']) ? DateTime::createFromFormat('m/d/y', $_GET['from'])->format('Y-m-d') : '';
$to   = isset($_GET['to'])   ? DateTime::createFromFormat('m/d/y', $_GET['to'])->format('Y-m-d') : '';
$rate = isset($_GET['rate']) ? floatval($_GET['rate']) : 0;

$params = [];
$types = '';
$condition = '';

if (!empty($from) && !empty($to)) {
    $condition = "WHERE DATE(timestamp) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $types = 'ss';
}

$sql = "
    SELECT 
        employee_id AS ID,
        name,
        SEC_TO_TIME(SUM(TIME_TO_SEC(hours_worked))) AS total_worked
    FROM (
        SELECT 
            employee_id,
            name,
            DATE(timestamp) AS work_date,
            TIMEDIFF(MAX(timestamp), MIN(timestamp)) AS hours_worked
        FROM attendance_logs
        $condition
        GROUP BY employee_id, name, DATE(timestamp)
    ) AS daily_logs
    GROUP BY employee_id, name
    ORDER BY name ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
    
    @media print {
        .no-print,
        .navbar,
        .sidebar,
        .bg-dark,
        .btn,
        .form-control,
        .form-label,
        .form-select,
        .container-fluid > .row > div:first-child {
            display: none !important;
        }

        body, html {
            background: white !important;
        }

        table {
            width: 100%;
            font-size: 14px;
        }

        .container-fluid {
            margin: 0;
            padding: 0;
        }
    }
</>
</style>

<div class="container-fluid">
    <h2 class="mb-4">🧾 Total Working Hours and Payments</h2>

    <!-- Buttons & Filter Form -->
    <div class="no-print">
        <div class="mb-3 text-end">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Print / Save as PDF
            </button>
        </div>

        <form method="GET" action="dashboard.php" class="row g-3 mb-4">
            <!-- Hidden to maintain ?page=report-summary -->
            <input type="hidden" name="page" value="report-summary">

            <div class="col-md-2">
                <label for="fromDate">From:</label>
                <input type="text" id="fromDate" name="from"
                    value="<?= $from ? date('m/d/y', strtotime($from)) : '' ?>"
                    class="form-control">
            </div>

            <div class="col-md-2">
                <label for="toDate">To:</label>
                <input type="text" id="toDate" name="to"
                    value="<?= $to ? date('m/d/y', strtotime($to)) : '' ?>"
                    class="form-control">
            </div>

            <div class="col-md-3">
                <label>Salary per Hour (₱):</label>
                <input type="number" name="rate" step="0.01" value="<?= htmlspecialchars($rate) ?>" class="form-control" required>
            </div>

            <div class="col-md-5 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Generate</button>
                <a href="dashboard.php?page=report-summary" class="btn btn-secondary me-2">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table Results -->
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Total Hours Worked</th>
                <th>Salary per Hour (₱)</th>
                <th>Total Payment (₱)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()):
                $total_worked = $row['total_worked'];
                [$h, $m, $s] = explode(":", $total_worked);
                $total_hours = $h + ($m / 60) + ($s / 3600);
                $payment = $rate * $total_hours;
            ?>
                <tr>
                    <td><?= $row['ID'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $total_worked ?></td>
                    <td>₱<?= number_format($rate, 2) ?></td>
                    <td>₱<?= number_format($payment, 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#fromDate", { dateFormat: "m/d/y" });
    flatpickr("#toDate", { dateFormat: "m/d/y" });

    function formatDateToWords(dateStr) {
        const options = { month: 'long', day: 'numeric' };
        return new Date(dateStr).toLocaleDateString('en-US', options);
    }

    const from = document.getElementById("fromDate")?.value;
    const to = document.getElementById("toDate")?.value;

    if (from && to) {
        const fromFormatted = formatDateToWords(from);
        const toFormatted = formatDateToWords(to);
        document.title = `Working Hours Report - From ${fromFormatted} to ${toFormatted}`;
    }
    
</script>
