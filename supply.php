<?php
require 'config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['blood_type'] ?? '');
    $units = intval($_POST['units'] ?? 0);
    if ($type !== '' && $units >= 0) {
        $stmt = $pdo->prepare('INSERT INTO supplies (blood_type,units) VALUES (?,?) ON DUPLICATE KEY UPDATE units = ?');
        $stmt->execute([$type, $units, $units]);
        flash_set('success', 'Supply updated.');
    } else {
        flash_set('error', 'Invalid input.');
    }
    header('Location: supply.php');
    exit;
}

$supplies = $pdo->query('SELECT * FROM supplies ORDER BY blood_type')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Supply Management — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h4>Supply Management</h4>
  <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?=htmlspecialchars($m)?></div><?php endif; ?>
  <?php if ($e = flash_get('error')): ?><div class="alert alert-danger"><?=htmlspecialchars($e)?></div><?php endif; ?>

  <div class="table-responsive shadow-sm">
    <table class="table table-striped">
      <thead class="table-light"><tr><th>Blood Type</th><th>Units</th><th>Update</th></tr></thead>
      <tbody>
        <?php foreach ($supplies as $s): ?>
          <tr>
            <td><?=htmlspecialchars($s['blood_type'])?></td>
            <td><?=intval($s['units'])?></td>
            <td style="width:320px;">
              <form method="post" class="d-flex">
                <input type="hidden" name="blood_type" value="<?=htmlspecialchars($s['blood_type'])?>">
                <input type="number" name="units" class="form-control form-control-sm me-2" value="<?=intval($s['units'])?>" min="0">
                <button class="btn btn-sm btn-danger">Save</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <!-- quick add new blood type -->
        <tr>
          <form method="post">
            <td><input name="blood_type" class="form-control form-control-sm" placeholder="New type (e.g. X+)"></td>
            <td><input name="units" type="number" class="form-control form-control-sm" value="0" min="0"></td>
            <td><button class="btn btn-sm btn-outline-danger">Add</button></td>
          </form>
        </tr>
      </tbody>
    </table>
  </div>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
