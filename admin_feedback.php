<?php
require 'config.php';
require_role('admin');

// ✅ Fetch all feedback with user details
$stmt = $pdo->query("SELECT f.*, u.name FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
$feedbacks = $stmt->fetchAll();

// ✅ Average rating
$avg = $pdo->query("SELECT AVG(rating) FROM feedback")->fetchColumn();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Feedback — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h3>📊 User Feedback</h3>
  <p><strong>Average Rating:</strong> <?= number_format($avg, 1) ?> ⭐</p>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>User</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($feedbacks as $fb): ?>
          <tr>
            <td><?= htmlspecialchars($fb['name']) ?></td>
            <td><?= str_repeat("⭐", $fb['rating']) ?></td>
            <td><?= htmlspecialchars($fb['comment']) ?></td>
            <td><?= $fb['created_at'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
