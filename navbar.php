<?php
// navbar.php - shared top navigation (include where needed)
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">BloodSave</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Dashboard</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="donor_search.php">Search Donors</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="donor_search.php">Search Donors</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Sign up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
