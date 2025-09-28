<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['user_id'];

// Fetch user points
$stmt = $pdo->prepare("SELECT points FROM reward WHERE user_id=?");
$stmt->execute([$uid]);
$user = $stmt->fetch();
$points = $user ? $user['points'] : 0;

// Fetch available rewards
$rewards = $pdo->query("SELECT * FROM donation_rewards")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rewards</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
      body {
          background: linear-gradient(135deg, #fce4ec, #f8bbd0);
          font-family: 'Segoe UI', sans-serif;
      }
      .points-box {
          background: #fff;
          border-radius: 15px;
          padding: 20px;
          text-align: center;
          box-shadow: 0 6px 18px rgba(0,0,0,0.1);
          margin-bottom: 30px;
      }
      .points-box h2 {
          font-weight: bold;
          color: #d81b60;
      }
      .reward-card {
          border-radius: 15px;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      .reward-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      }
      .reward-card h5 {
          color: #d81b60;
          font-weight: bold;
      }
      .btn-success {
          border-radius: 30px;
          padding: 8px 18px;
      }
      .btn-secondary {
          border-radius: 30px;
          padding: 8px 18px;
      }
  </style>
</head>
<body class="container py-5">

  <!-- User Points Display -->
  <div class="points-box">
      <h2>🌟 Your Points: <?= $points ?></h2>
      <p class="text-muted">Keep donating to earn more rewards 🎁</p>
  </div>

  <!-- Rewards Grid -->
  <h3 class="mb-4 text-center text-dark fw-bold">Available Rewards</h3>
  <div class="row">
    <?php foreach ($rewards as $r): ?>
      <div class="col-md-4 mb-4">
        <div class="card reward-card h-100 p-3">
          <div class="card-body text-center">
            <h5 class="card-title"><?= htmlspecialchars($r['name']) ?></h5>
            <p class="card-text">✨ Cost: <b><?= $r['points'] ?></b> Points</p>
            <?php if ($points >= $r['points']): ?>
              <a href="redeem.php?id=<?= $r['reward_id'] ?>" class="btn btn-success">Redeem</a>
            <?php else: ?>
              <button class="btn btn-secondary" disabled>Not Enough Points</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</body>
</html>
