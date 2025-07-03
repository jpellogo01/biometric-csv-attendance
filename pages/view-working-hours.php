<?php
// Already inside dashboard.php, so no need to start session or reconnect DB

$searchName = isset($_GET['name']) ? trim($_GET['name']) : '';
$searchDate = '';
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $parsed = DateTime::createFromFormat('m/d/y', $_GET['date']);
    if ($parsed) {
        $searchDate = $parsed->format('Y-m-d');
    }
}

$sql = "
    SELECT 
        employee_id AS ID,
        name,
        DATE(timestamp) AS date,
        MIN(TIME(timestamp)) AS time_in,
        MAX(TIME(timestamp)) AS time_out,
        TIMEDIFF(MAX(timestamp), MIN(timestamp)) AS hours_worked
    FROM attendance_logs
    WHERE 1
";

if (!empty($searchName)) {
    $sql .= " AND name LIKE '%" . $conn->real_escape_string($searchName) . "%'";
}
if (!empty($searchDate)) {
    $sql .= " AND DATE(timestamp) = '" . $conn->real_escape_string($searchDate) . "'";
}

$sql .= "
    GROUP BY employee_id, DATE(timestamp)
    ORDER BY DATE(timestamp) DESC
";

$result = $conn->query($sql);
?>

<div class="container-fluid">
    <h2 class="mb-4">🕒 Working Hours Report</h2>

    <!-- Search form -->
    <form class="row g-3 mb-4" method="GET">
        <input type="hidden" name="page" value="view-working-hours">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Search by Name" value="<?= htmlspecialchars($searchName) ?>">
        </div>
        <div class="col-md-4">
            <input type="text" id="searchDate" name="date" class="form-control"
                value="<?= $searchDate ? date('m/d/y', strtotime($searchDate)) : '' ?>"
                placeholder="Select date">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="dashboard.php?page=view-working-hours" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours Worked</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()):
                        $diff = explode(":", $row['hours_worked']);
                        $hours = (int)$diff[0];
                        $minutes = (int)$diff[1];
                        $formatted_work = "{$hours} hrs {$minutes} min";
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ID']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['time_in']) ?></td>
                            <td><?= htmlspecialchars($row['time_out']) ?></td>
                            <td><?= $formatted_work ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#searchDate", {
        dateFormat: "m/d/y"
    });

    function formatDateToWords(dateStr) {
        const options = {
            month: 'long',
            day: 'numeric'
        };
        return new Date(dateStr).toLocaleDateString('en-US', options);
    }

    const search = document.getElementById("searchDate")?.value;

    if (search) {
        const searchFormatted = formatDateToWords(search);
        document.title = `Working Hours Report - ${searchFormatted}`;
    }
</script>