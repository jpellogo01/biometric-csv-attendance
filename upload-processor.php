<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "attendance_system";

// Connect to database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("<script>alert('❌ Database connection failed!');</script>");
}

if (isset($_POST["submit"])) {
    if (is_uploaded_file($_FILES["csv_file"]["tmp_name"])) {
        $file = fopen($_FILES["csv_file"]["tmp_name"], "r");
        $row = 0;
        $successCount = 0;
        $duplicateCount = 0;

        while (($data = fgetcsv($file, 1000, "\t")) !== FALSE) {
            if ($row == 0) {
                $row++;
                continue;
            }
            $employee_id = trim($data[0]);
            $name = trim($data[1]);
            $department = trim($data[2]);
            $timestamp = trim(preg_replace('/\s+/', ' ', $data[3])); // normalize spaces
            $device_id = trim($data[4]);

            $datetimeObj = DateTime::createFromFormat('m-d-Y H:i:s', $timestamp);
            if (!$datetimeObj) continue; // skip malformed rows
            $datetime = $datetimeObj->format('Y-m-d H:i:s');

            // Check for duplicates
            $check = $conn->prepare("SELECT id FROM attendance_logs WHERE employee_id = ? AND timestamp = ?");
            $check->bind_param("is", $employee_id, $datetime);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $duplicateCount++;
            } else {
                $stmt = $conn->prepare("INSERT INTO attendance_logs (employee_id, name, department, timestamp, device_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("isssi", $employee_id, $name, $department, $datetime, $device_id);
                if ($stmt->execute()) {
                    $successCount++;
                }
                $stmt->close();
            }

            $check->close();
        }

        fclose($file);
        echo "<script>alert('✅ Imported: $successCount record(s)\n⚠️ Duplicates skipped: $duplicateCount'); window.location.href='upload-form.php';</script>";
    } else {
        echo "<script>alert('❌ No file uploaded.'); window.location.href='upload-form.php';</script>";
    }
}
