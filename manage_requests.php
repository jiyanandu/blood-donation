<?php
require 'config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id > 0 && $action) {
        if ($action === 'approve') {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare('SELECT * FROM donations WHERE id = ? FOR UPDATE');
                $stmt->execute([$id]);
                $don = $stmt->fetch();
                if ($don && $don['status'] === 'pending') {
                    $pdo->prepare('UPDATE donations SET status = ? WHERE id = ?')->execute(['approved', $id]);
                    $up = $pdo->prepare('INSERT INTO supplies (blood_type,units) VALUES (?,?) ON DUPLICATE KEY UPDATE units = units + VALUES(units)');
                    $up->execute([$don['blood_type'], $don['units']]);
                }
                $pdo->commit();
                flash_set('success', 'Request approved.');
            } catch (Exception $e) {
                $pdo->rollBack();
                flash_set('error', 'Error approving request: ' . $e->getMessage());
            }
        } elseif ($action === 'reject') {
            $pdo->prepare('UPDATE donations SET status = ? WHERE id = ?')->execute(['rejected', $id]);
            flash_set('success', 'Request rejected.');
        }
    }
    header('Location: manage_requests.php');
    exit;
}

$rows = $pdo->query('SELECT d.*, u.name AS username FROM donations d JOIN users u ON u.id = d.user_id ORDER BY d.created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Requests — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h4 class="mb-3">Manage Donation Requests</h4>
  <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?=htmlspecialchars($m)?></div><?php endif; ?>
  <?php if ($e = flash_get('error')): ?><div class="alert alert-danger"><?=htmlspecialchars($e)?></div><?php endif; ?>

  <div class="table-responsive shadow-sm">
    <table class="table table-striped align-middle">
      <thead class="table-light"><tr><th>User</th><th>Type</th><th>Units</th><th>Notes</th><th>Status</th><th>When</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['username'])?></td>
            <td><?=htmlspecialchars($r['blood_type'])?></td>
            <td><?=intval($r['units'])?></td>
            <td><?=htmlspecialchars($r['notes'])?></td>
            <td>
              <?php if ($r['status'] === 'approved'): ?>
                <span class="badge bg-success">Approved</span>
              <?php elseif ($r['status'] === 'rejected'): ?>
                <span class="badge bg-danger">Rejected</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php endif; ?>
            </td>
            <td><?=htmlspecialchars($r['created_at'])?></td>
            <td>
              <?php if ($r['status'] === 'pending'): ?>
                <form method="post" style="display:inline-block">
                  <input type="hidden" name="id" value="<?=intval($r['id'])?>">
                  <button name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                </form>
                <form method="post" style="display:inline-block">
                  <input type="hidden" name="id" value="<?=intval($r['id'])?>">
                  <button name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                </form>
              <?php else: ?>
                <span class="text-muted small">No actions</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
