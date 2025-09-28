<?php
require 'config.php';
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; }

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'add') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $stmt = $pdo->prepare('INSERT INTO health_tips (title, content) VALUES (?, ?)');
        $stmt->execute([$title, $content]);
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $pdo->prepare('DELETE FROM health_tips WHERE id=?')->execute([$id]);
    } elseif ($action === 'edit') {
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $content = $_POST['content'];
        $stmt = $pdo->prepare('UPDATE health_tips SET title=?, content=? WHERE id=?');
        $stmt->execute([$title, $content, $id]);
    }
    header('Location: admin_health_tips.php'); exit;
}

// fetch tips
$tips = $pdo->query('SELECT * FROM health_tips ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Health Tips</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
<h3>Manage Health Tips</h3>

<!-- Add New Tip Form -->
<div class="card mb-4 p-3">
<form method="post">
<input type="hidden" name="action" value="add">
<div class="mb-2"><input name="title" class="form-control" placeholder="Tip Title" required></div>
<div class="mb-2"><textarea name="content" class="form-control" placeholder="Tip Content" required></textarea></div>
<button class="btn btn-success">Add Tip</button>
</form>
</div>

<!-- Existing Tips -->
<?php foreach($tips as $tip): ?>
<div class="card mb-2">
<div class="card-body">
<form method="post" class="d-flex gap-2 align-items-start">
<input type="hidden" name="id" value="<?=intval($tip['id'])?>">
<input type="hidden" name="action" value="edit">
<div class="flex-grow-1">
<input name="title" class="form-control mb-1" value="<?=htmlspecialchars($tip['title'])?>">
<textarea name="content" class="form-control"><?=htmlspecialchars($tip['content'])?></textarea>
</div>
<button class="btn btn-primary">Update</button>
</form>
<form method="post" class="mt-1">
<input type="hidden" name="id" value="<?=intval($tip['id'])?>">
<input type="hidden" name="action" value="delete">
<button class="btn btn-danger">Delete</button>
</form>
</div>
</div>
<?php endforeach; ?>
<a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back</a>
</div>
</body>
</html>
