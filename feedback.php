<?php
require 'config.php';
require_role('user');

// ✅ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$message = "";

// ✅ Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $stmt = $pdo->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
    if ($stmt->execute([$uid, $rating, $comment])) {
        $message = "✅ Feedback submitted successfully!";
    } else {
        $message = "❌ Error submitting feedback.";
    }
}

// ✅ Fetch user’s feedback history
$stmt = $pdo->prepare("SELECT * FROM feedback WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$uid]);
$feedbacks = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Feedback — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h3>📝 Feedback</h3>
  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- Feedback Form -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title">Give Your Feedback</h5>
      <form method="post">
        <div class="mb-2">
          <label class="form-label">Rating</label>
          <select name="rating" class="form-select" required>
            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
            <option value="4">⭐⭐⭐⭐ Good</option>
            <option value="3">⭐⭐⭐ Average</option>
            <option value="2">⭐⭐ Poor</option>
            <option value="1">⭐ Very Bad</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label">Comment</label>
          <textarea name="comment" class="form-control" rows="3" placeholder="Write your feedback..."></textarea>
        </div>
        <button class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>

  <!-- Previous Feedback -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title">Your Previous Feedback</h5>
      <?php if ($feedbacks): ?>
        <ul class="list-group">
          <?php foreach ($feedbacks as $fb): ?>
            <li class="list-group-item">
              <strong>Rating:</strong> <?= str_repeat("⭐", $fb['rating']) ?><br>
              <?= htmlspecialchars($fb['comment']) ?><br>
              <small class="text-muted"><?= $fb['created_at'] ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No feedback given yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
