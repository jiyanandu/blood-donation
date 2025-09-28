<?php
require 'config.php';
require_logged_in();

// Handle create donor / remove donor and donation requests in one endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['user_id'];

    // Create donor record (user wants to be listed as donor)
    if (!empty($_POST['create_donor'])) {
        $b = $_POST['donor_blood_type'] ?? '';
        $units = max(1, intval($_POST['donor_units'] ?? 1));
        $name = $_SESSION['name'];
        $phone = ''; // optional: fetch from users table or form
        // Upsert donor for this user
        $stmt = $pdo->prepare('INSERT INTO donors (user_id,name,blood_type,units,phone) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), blood_type=VALUES(blood_type), units=VALUES(units)');
        $stmt->execute([$uid, $name, $b, $units, $phone]);
        flash_set('success', 'You are now listed as a donor.');
    }

    // Remove donor record
    if (!empty($_POST['remove_donor'])) {
        $del = $pdo->prepare('DELETE FROM donors WHERE user_id = ?');
        $del->execute([$uid]);
        flash_set('success', 'Your donor record was removed.');
    }

    // Standard donation request (user requests to donate / offer)
    if (!empty($_POST['blood_type']) && !empty($_POST['units']) && empty($_POST['create_donor'])) {
        $blood_type = $_POST['blood_type'];
        $units = max(1, intval($_POST['units']));
        $notes = trim($_POST['notes'] ?? '');
        $stmt = $pdo->prepare('INSERT INTO donations (user_id,blood_type,units,notes) VALUES (?,?,?,?)');
        $stmt->execute([$uid, $blood_type, $units, $notes]);
        flash_set('success', 'Donation request submitted.');
    }
}
header('Location: user_dashboard.php');
exit;
