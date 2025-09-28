<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $role = ($_POST['role'] === 'admin') ? 'admin' : 'user';

    if (!$name || !$email || !$password) {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Provide a valid email.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name,email,password,role,phone) VALUES (?,?,?,?,?)');
        try {
            $stmt->execute([$name,$email,$hash,$role,$phone]);
            flash_set('success', 'Registration successful. Please login.');
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) $error = 'Email already registered.';
            else $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register — BloodSave</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h4 class="mb-3">Create an account</h4>
          <?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Full name</label>
              <input name="name" class="form-control" value="<?=htmlspecialchars($name ?? '')?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" value="<?=htmlspecialchars($email ?? '')?>" required>
            </div>
            <div class="mb-3 row">
              <div class="col">
                <label class="form-label">Phone</label>
                <input name="phone" class="form-control" value="<?=htmlspecialchars($phone ?? '')?>">
              </div>
              <div class="col">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                  <option value="user" <?= (($_POST['role'] ?? '')==='user') ? 'selected' : '' ?>>User</option>
                  <option value="admin" <?= (($_POST['role'] ?? '')==='admin') ? 'selected' : '' ?>>Admin</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <button class="btn btn-danger">Register</button>
              <a href="login.php">Already have an account?</a>
            </div>
          </form>
        </div>
      </div>
      <?php if ($msg = flash_get('success')): ?><div class="alert alert-success mt-3"><?=htmlspecialchars($msg)?></div><?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
