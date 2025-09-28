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

// ✅ fetch user's requests
$stmt = $pdo->prepare('SELECT * FROM donations WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([$uid]);
$requests = $stmt->fetchAll();

// ✅ donor record
$donorStmt = $pdo->prepare('SELECT * FROM donors WHERE user_id = ? LIMIT 1');
$donorStmt->execute([$uid]);
$myDonor = $donorStmt->fetch();

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 15px; }
    .dashboard-title { font-weight: 600; }
    .dashboard-actions a { margin-left: 10px; }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container py-4">
  <!-- Greeting -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h3 class="dashboard-title">👋 Welcome, <?=htmlspecialchars($_SESSION['name'])?></h3>
    <div class="dashboard-actions">
      <a href="donor_search.php" class="btn btn-danger mb-2">
        <i class="bi bi-search-heart"></i> Find Donors
      </a>
      <a href="health_tips.php" class="btn btn-info mb-2">
        <i class="bi bi-heart-pulse"></i> Health Tips & Awareness
      </a>
      <a href="user_camps.php" class="btn btn-warning mb-2">
        <i class="bi bi-calendar-event"></i> Blood Donation Camps
      </a>
      <a href="feedback.php" class="btn btn-secondary mb-2">
        📝 Feedback
      </a>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-6">
      
      <!-- Send Donation Request -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-droplet"></i> Send Donation Request</h5>
          <form method="post" action="donate.php">
            <div class="mb-2">
              <label class="form-label">Blood Type</label>
              <select name="blood_type" class="form-select" required>
                <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Units</label>
              <input name="units" type="number" class="form-control" value="1" min="1" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Notes (optional)</label>
              <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            <div class="d-flex justify-content-between">
              <button class="btn btn-success"><i class="bi bi-send"></i> Submit</button>
              <a href="donor_search.php" class="btn btn-outline-secondary"><i class="bi bi-search"></i> Search Donors</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Register as Donor -->
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-person-plus"></i> Register as Donor</h5>
          <?php if ($myDonor): ?>
            <p class="mb-2">✅ You are registered as donor:
              <strong><?=htmlspecialchars($myDonor['blood_type'] ?? '')?></strong> 
              (<?=intval($myDonor['units'] ?? 0)?> unit(s))
            </p>
            <form method="post" action="donate.php" onsubmit="return confirm('Remove donor?');">
              <input type="hidden" name="remove_donor" value="1">
              <button class="btn btn-danger"><i class="bi bi-trash"></i> Remove Donor Record</button>
            </form>
          <?php else: ?>
            <form method="post" action="donate.php" class="row g-2">
              <input type="hidden" name="create_donor" value="1">
              <div class="col-6">
                <select name="donor_blood_type" class="form-select" required>
                  <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                  <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                </select>
              </div>
              <div class="col-3">
                <input name="donor_units" type="number" class="form-control" min="1" value="1" required>
              </div>
              <div class="col-3">
                <button class="btn btn-primary w-100">Register</button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-6">

      <!-- Donation Requests -->
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-clipboard2-pulse"></i> Your Donation Requests</h5>
          <ul class="list-group">
            <?php if (!empty($requests)): foreach ($requests as $r): ?>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-bold"><?=htmlspecialchars($r['blood_type'])?> • <?=intval($r['units'])?> unit(s)</div>
                  <div class="small text-muted"><?=htmlspecialchars($r['notes'])?></div>
                </div>
                <div class="text-end">
                  <?php if ($r['status'] === 'approved'): ?>
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                  <?php elseif ($r['status'] === 'rejected'): ?>
                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Pending</span>
                  <?php endif; ?>
                  <div class="small text-muted mt-1"><?=htmlspecialchars($r['created_at'])?></div>
                </div>
              </li>
            <?php endforeach; else: ?>
              <li class="list-group-item">No requests yet. Use the form to send a donation request.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
