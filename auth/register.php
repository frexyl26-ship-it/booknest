<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) {
    redirect('/index.php');
}
$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (strlen($name) < 2 || strlen($name) > 100) {
        $errors[] = 'Name must be 2 to 100 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Password must be at least 8 characters with letters and numbers.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'That email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'member')")
               ->execute([$name, $email, $hash]);
            flash('success', 'Account created! You can now login.');
            redirect('/auth/login.php');
        }
    }
}
$page_title = 'Sign Up - BookNest';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8">
  <h1 class="text-2xl font-bold mb-1">Create an account</h1>
  <p class="text-sm text-gray-500 mb-6">Join BookNest to borrow books.</p>
  <?php foreach ($errors as $err): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-3"><?php echo e($err); ?></div>
  <?php endforeach; ?>
  <form method="POST" data-validate novalidate>
    <?php echo csrf_field(); ?>
    <label class="block text-sm font-medium mb-1" for="name">Full name</label>
    <input type="text" id="name" name="name" value="<?php echo e($name); ?>" required minlength="2" maxlength="100"
           class="w-full border rounded-lg px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-brand">
    <label class="block text-sm font-medium mb-1" for="email">Email</label>
    <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required
           class="w-full border rounded-lg px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-brand">
    <label class="block text-sm font-medium mb-1" for="password">Password</label>
    <input type="password" id="password" name="password" required minlength="8"
           class="w-full border rounded-lg px-4 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-brand">
    <p class="text-xs text-gray-400 mb-4">At least 8 characters, with letters and numbers.</p>
    <label class="block text-sm font-medium mb-1" for="confirm">Confirm password</label>
    <input type="password" id="confirm" name="confirm" required
           class="w-full border rounded-lg px-4 py-2 mb-6 focus:outline-none focus:ring-2 focus:ring-brand">
    <button class="w-full bg-brand text-white py-2 rounded-lg hover:bg-brand-dark transition">Sign Up</button>
  </form>
  <p class="text-sm text-gray-500 text-center mt-6">Already have an account? <a href="<?php echo url('/auth/login.php'); ?>" class="text-brand hover:underline">Login</a></p>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
