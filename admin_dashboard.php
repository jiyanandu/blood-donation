<?php
require 'config.php';
require_role('admin');

// counts and supplies
$donorCount = intval($pdo->query('SELECT COUNT(*) FROM donors')->fetchColumn());
$pendingCount = intval($pdo->query("SELECT COUNT(*) FROM donations WHERE status='pending'")->fetchColumn());
$totalUsers = intval($pdo->query('SELECT COUNT(*) FROM users')->fetchColumn());
$supplies = $pdo->query('SELECT * FROM supplies ORDER BY blood_type')->fetchAll();

// Prepare data for Chart.js
$bloodTypes = [];
$units = [];
foreach ($supplies as $s) {
    $bloodTypes[] = $s['blood_type'];
    $units[] = intval($s['units']);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .card:hover { transform: scale(1.02); transition: 0.3s; }
    .progress { height: 12px; }
  </style>
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Admin Dashboard</h3>
    <a href="manage_requests.php" class="btn btn-danger">
      Manage Requests (<?= $pendingCount ?>)
    </a>
  </div>

  <!-- Stats Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary shadow-sm p-3">
        <div class="d-flex align-items-center">
          <i class="bi bi-people-fill fs-1 me-3"></i>
          <div>
            <small>Total Users</small>
            <h4><?= $totalUsers ?></h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success shadow-sm p-3">
        <div class="d-flex align-items-center">
          <i class="bi bi-droplet-fill fs-1 me-3"></i>
          <div>
            <small>Total Donors</small>
            <h4><?= $donorCount ?></h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning shadow-sm p-3">
        <div class="d-flex align-items-center">
          <i class="bi bi-exclamation-triangle-fill fs-1 me-3"></i>
          <div>
            <small>Pending Requests</small>
            <h4><?= $pendingCount ?></h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-danger shadow-sm p-3">
        <div class="d-flex align-items-center">
          <i class="bi bi-box-seam fs-1 me-3"></i>
          <div>
            <small>Manage Supply</small>
            <a href="supply.php" class="btn btn-light btn-sm mt-1">Edit Supply</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Module Buttons -->
    <div class="col-md-3">
      <div class="card p-3 shadow-sm">
        <h5>Manage Health Tips</h5>
        <a class="btn btn-warning mt-2" href="admin_health_tips.php">
          <i class="bi bi-heart-pulse"></i> Go
        </a>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3 shadow-sm">
        <h5>Manage Blood Donation Camps</h5>
        <a class="btn btn-info mt-2" href="admin_camps.php">
          <i class="bi bi-calendar-event"></i> Go
        </a>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3 shadow-sm">
        <h5>Manage Feedback</h5>
        <a class="btn btn-secondary mt-2" href="admin_feedback.php">
          <i class="bi bi-chat-left-text"></i> View Feedback
        </a>
      </div>
    </div>

  </div>

  <!-- Blood Supply Table -->
  <h5 class="mb-2">Current Supply Levels</h5>
  <input type="text" id="searchBlood" onkeyup="searchTable()" placeholder="Search blood type..." class="form-control mb-3 w-50 shadow-sm">

  <div class="table-responsive shadow-sm mb-4">
    <table class="table table-hover align-middle" id="supplyTable">
      <thead class="table-dark">
        <tr><th>Blood Type</th><th>Units</th><th>Progress</th><th>Last Updated</th></tr>
      </thead>
      <tbody>
        <?php foreach ($supplies as $s): ?>
          <tr class="<?= $s['units'] < 5 ? 'table-danger' : '' ?>">
            <td><?=htmlspecialchars($s['blood_type'])?></td>
            <td><span class="badge bg-<?= $s['units'] < 5 ? 'danger' : 'success' ?>"><?=intval($s['units'])?></span></td>
            <td>
              <div class="progress">
                <div class="progress-bar bg-<?= $s['units'] < 5 ? 'danger' : 'success' ?>" role="progressbar" style="width: <?= min(100, $s['units']*5) ?>%"></div>
              </div>
            </td>
            <td><?=htmlspecialchars($s['last_updated'])?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Charts Section -->
  <div class="row">
    <div class="col-md-6">
      <div class="card shadow-sm p-3">
        <h6 class="text-center">Blood Supply (Bar Chart)</h6>
        <canvas id="barChart"></canvas>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm p-3">
        <h6 class="text-center">Blood Supply Distribution (Doughnut)</h6>
        <canvas id="pieChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Chart.js Data
const bloodTypes = <?= json_encode($bloodTypes) ?>;
const units = <?= json_encode($units) ?>;

// Bar Chart
new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {
    labels: bloodTypes,
    datasets: [{
      label: 'Units Available',
      data: units,
      backgroundColor: 'rgba(220, 53, 69, 0.7)',
      borderColor: 'rgba(220, 53, 69, 1)',
      borderWidth: 1
    }]
  },
  options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

// Doughnut Chart
new Chart(document.getElementById('pieChart'), {
  type: 'doughnut',
  data: {
    labels: bloodTypes,
    datasets: [{
      label: 'Blood Distribution',
      data: units,
      backgroundColor: [
        '#dc3545','#fd7e14','#ffc107','#198754',
        '#0dcaf0','#6f42c1','#20c997','#6f42c1'
      ]
    }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Search Filter
function searchTable() {
  let input = document.getElementById("searchBlood").value.toUpperCase();
  let rows = document.getElementById("supplyTable").getElementsByTagName("tr");
  for (let i = 1; i < rows.length; i++) {
    let td = rows[i].getElementsByTagName("td")[0];
    if (td) {
      rows[i].style.display = td.textContent.toUpperCase().indexOf(input) > -1 ? "" : "none";
    }
  }
}
</script>
</body>
</html>
