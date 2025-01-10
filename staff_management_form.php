<?php
// Simulated staff data for validation (replace this with database queries)
$staffList = [
    ['id' => '123', 'name' => 'John Doe'],
    ['id' => '456', 'name' => 'Jane Smith'],
    ['id' => '789', 'name' => 'Alice Brown']
];

$query = $_GET['query'] ?? '';
$result = null;

// Validate the query and fetch staff data
foreach ($staffList as $staff) {
    if ($staff['id'] === $query || stripos($staff['name'], $query) !== false) {
        $result = $staff;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="p-4">
        <?php if ($result): ?>
            <h5 class="text-success mb-3"><i class="bi bi-gear me-2"></i>Create Username and Password</h5>
            <div class="card p-3">
                <form action="process_username_password.php" method="post">
                    <input type="hidden" name="staff_id" value="<?= htmlspecialchars($result['id']) ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Staff Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($result['name']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Create</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">Staff not found. Please try again.</div>
        <?php endif; ?>
    </div>
</body>
</html>
