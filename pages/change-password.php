<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" role="alert">
        ✅ Password changed successfully.
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        ❌ <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<div class="card shadow p-4 mx-auto" style="max-width: 500px;">
    <h4 class="mb-3 text-center"><i class="fa-solid fa-key"></i> Change Password</h4>

    <form action="change-password-handler.php" method="POST">
        <div class="mb-3">
            <label for="old_password" class="form-label">Current Password</label>
            <input type="password" name="old_password" id="old_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
    </form>
</div>
