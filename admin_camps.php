<?php
require 'config.php';
require_role('admin');

// Handle new camp creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $stmt = $pdo->prepare('INSERT INTO blood_camps (name, location, camp_date, camp_time, notes) VALUES (?,?,?,?,?)');
    $stmt->execute([
        $_POST['name'],
        $_POST['location'],
        $_POST['camp_date'],
        $_POST['camp_time'],
        $_POST['notes']
    ]);
}

// fetch all camps
$camps = $pdo->query('SELECT * FROM blood_camps ORDER BY camp_date DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <title>Admin — Manage Blood Camps</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h3>Manage Blood Donation Camps</h3>
  
  <div class="card p-3 mb-4">
    <h5>Create New Camp</h5>
    <form method="post">
      <div class="mb-2"><input name="name" placeholder="Camp Name" class="form-control" required></div>
      <div class="mb-2"><input name="location" placeholder="Location" class="form-control" required></div>
      <div class="mb-2"><input type="date" name="camp_date" class="form-control" required></div>
      <div class="mb-2"><input type="time" name="camp_time" class="form-control" required></div>
      <div class="mb-2"><textarea name="notes" class="form-control" placeholder="Notes"></textarea></div>
      <button class="btn btn-primary">Create Camp</button>
    </form>
  </div>

  <h5>Existing Camps</h5>
  <table class="table table-striped">
    <thead><tr><th>Name</th><th>Location</th><th>Date</th><th>Time</th><th>Registrations</th></tr></thead>
    <tbody>
      <?php foreach($camps as $c): 
        $regCount = $pdo->prepare('SELECT COUNT(*) FROM camp_registrations WHERE camp_id=?');
        $regCount->execute([$c['id']]);
        $count = $regCount->fetchColumn();
      ?>
      <tr>
        <td><?=htmlspecialchars($c['name'])?></td>
        <td><?=htmlspecialchars($c['location'])?></td>
        <td><?=htmlspecialchars($c['camp_date'])?></td>
        <td><?=htmlspecialchars($c['camp_time'])?></td>
        <td><?=$count?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
