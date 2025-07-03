<div class="container">
    <div class="card shadow p-4" style="max-width: 600px; margin: 0 auto;">
        <h4 class="text-center mb-4">📄 Upload Attendance CSV</h4>

        <form method="POST" action="upload-processor.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="csv_file" class="form-label">Select CSV File</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv, .txt" required>
            </div>

            <div class="d-grid mb-2">
                <button type="submit" name="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
