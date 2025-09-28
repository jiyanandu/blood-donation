<?php
require 'config.php';
$term = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT * FROM donors';
if ($term !== '') {
    $sql .= ' WHERE name LIKE ? OR blood_type LIKE ? OR city LIKE ?';
    $like = "%$term%";
    $params = [$like, $like, $like];
}
$sql .= ' ORDER BY created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$donors = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Find Donors — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="row mb-3">
    <div class="col-md-8">
      <form class="input-group" method="get">
        <input name="q" value="<?=htmlspecialchars($term)?>" class="form-control" placeholder="Search by name, blood type or city">
        <button class="btn btn-danger">Search</button>
      </form>
    </div>
    <div class="col-md-4 text-end">
      <a class="btn btn-outline-secondary" href="donor_search.php">Reset</a>
    </div>
  </div>

  <?php if (!empty($donors)): ?>
    <div class="row g-3">
      <?php foreach ($donors as $d): ?>
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-start">
              <div>
                <h6 class="mb-1 fw-bold"><?=htmlspecialchars($d['name'])?> <small class="text-muted">• <?=htmlspecialchars($d['blood_type'])?></small></h6>
                <div class="small text-muted">City: <?=htmlspecialchars($d['city'] ?? '—')?></div>
                <div class="mt-2"><strong>Units:</strong> <?=intval($d['units'])?> &nbsp; <strong>Phone:</strong> <?=htmlspecialchars($d['phone'] ?? '—')?></div>
              </div>
              <div class="text-end">
                <div class="small text-muted"><?=htmlspecialchars($d['created_at'])?></div>
                <?php if (!empty($_SESSION['user_id'])): ?>
                  <a href="tel:<?=htmlspecialchars($d['phone'])?>" class="btn btn-sm btn-outline-danger mt-2">Call</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No donors found. Try a different query or register as a donor.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
