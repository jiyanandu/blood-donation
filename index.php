<?php require 'config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BloodSave — Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<header class="py-5 bg-danger text-white">
  <div class="container text-center">
    <h1 class="display-5 fw-bold">BloodSave</h1>
    <p class="lead">Connect donors, recipients and admins — save lives together.</p>
    <div class="mt-4">
      <?php if (empty($_SESSION['user_id'])): ?>
        <a class="btn btn-light btn-lg me-2" href="register.php">Get Started</a>
        <a class="btn btn-outline-light btn-lg" href="donor_search.php">Find Donors</a>
      <?php else: ?>
        <a class="btn btn-light btn-lg" href="<?= $_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php' ?>">Go to Dashboard</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container my-5">
  <div class="row gy-4">
    <div class="col-md-6">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Why donate?</h5>
          <p class="card-text">A single blood donation can save multiple lives. Join our community of donors and help hospitals maintain critical supply.</p>
          <a href="register.php" class="btn btn-danger">Become a Donor</a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Search donors nearby</h5>
          <p class="card-text">Quickly find donors by blood type and city. Admins can manage supplies and requests.</p>
          <a href="donor_search.php" class="btn btn-outline-danger">Search Donors</a>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="py-4 bg-white border-top">
  <div class="container text-center">
    <small class="text-muted">© <?=date('Y')?> BloodSave • Built with ❤️</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
