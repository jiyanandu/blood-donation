<?php
require 'config.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

// fetch tips
$tips = $pdo->query('SELECT * FROM health_tips ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Health Tips & Awareness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h3>Health Tips & Awareness</h3>
    <?php foreach($tips as $tip): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?=htmlspecialchars($tip['title'])?></h5>
                <p class="card-text"><?=nl2br(htmlspecialchars($tip['content']))?></p>
                <small class="text-muted">Added on <?=htmlspecialchars($tip['created_at'])?></small>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if(empty($tips)): ?>
        <div class="alert alert-info">No health tips available yet.</div>
    <?php endif; ?>
    <a href="user_dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>
