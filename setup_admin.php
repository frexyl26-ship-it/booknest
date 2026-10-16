<?php
// Run ONCE in the browser, then DELETE this file.
require_once __DIR__ . '/includes/functions.php';

$email = 'admin@booknest.com';
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    exit('Admin already exists. Please delete setup_admin.php.');
}
$hash = password_hash('Admin@123', PASSWORD_BCRYPT);
$db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')")
   ->execute(['Library Admin', $email, $hash]);
echo 'Admin created: admin@booknest.com / Admin@123 — now DELETE setup_admin.php';
