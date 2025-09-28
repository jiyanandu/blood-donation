<?php
require 'config.php';
require_role('user');

$uid = $_SESSION['user_id'];

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['camp_id'])) {
    $camp_id = intval($_POST['camp_id']);
    $units = max(1, intval($_POST['units']));

    // check if already registered
    $check = $pdo->prepare('SELECT * FROM camp_registrations WHERE camp_id=? AND user_id=?');
    $check->execute([$camp_id, $uid]);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO camp_registrations (camp_id, user_id, units) VALUES (?,?,?)');
        $stmt->execute([$camp_id, $uid, $units]);
    }
}

// fetch upcoming camps
$camps = $pdo->query('SELECT * FROM blood_camps WHERE camp_date >= CURDATE() ORDER BY camp_date ASC')->fetchAll();

// fetch user registrations
$myRegs = $pdo->prepare('SELECT cr.*, bc.name, bc.location, bc.camp_date, bc.camp_time 
                         FROM camp_registrations cr 
                         JOIN blood_camps bc ON bc.id = cr.camp_id 
                         WHERE cr.user_id=? ORDER BY bc.camp_date ASC');
$myRegs->execute([$uid]);
$myRegistrations = $myRegs->fetchAll();
?>
<!doctype html>
<html>
<head>
  <title>Blood Donation Camps</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h3>Upcoming Blood Donation Camps</h3>
  <div class="row g-3">
    <?php foreach($camps as $c): ?>
    <div class="col-md-6">
      <div class="card p-3 shadow-sm">
        <h5><?=htmlspecialchars($c['name'])?></h5>
        <p>Location: <?=htmlspecialchars($c['location'])?></p>
        <p>Date: <?=htmlspecialchars($c['camp_date'])?> | Time: <?=htmlspecialchars($c['camp_time'])?></p>
        <p><?=htmlspecialchars($c['notes'])?></p>
        <form method="post">
          <input type="hidden" name="camp_id" value="<?=$c['id']?>">
          <div class="input-group mb-2">
            <input type="number" name="units" class="form-control" value="1" min="1">
            <button class="btn btn-success">Register</button>
          </div>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <h4 class="mt-4">My Camp Registrations</h4>
  <ul class="list-group mb-4">
    <?php foreach($myRegistrations as $r): ?>
      <li class="list-group-item">
        <?=htmlspecialchars($r['name'])?> at <?=htmlspecialchars($r['location'])?> on <?=htmlspecialchars($r['camp_date'])?> 
        (<?=intval($r['units'])?> unit(s))
      </li>
    <?php endforeach; ?>
    <?php if(empty($myRegistrations)) echo "<li class='list-group-item'>No registrations yet.</li>"; ?>
  </ul>
</div>
</body>
</html>
