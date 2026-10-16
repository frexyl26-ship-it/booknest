<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) {
    redirect('/index.php');
}
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($password === '') {
        $errors[] = 'Please enter your password.';
    }

    if (!$errors) {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            redirect($user['role'] === 'admin' ? '/admin/index.php' : '/index.php');
        }
        $errors[] = 'Invalid email or password.';
    }
}
$page_title = 'Login - BookNest';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8">
  <h1 class="text-2xl font-bold mb-1">Welcome back</h1>
  <p class="text-sm text-gray-500 mb-6">Login to borrow books.</p>
  <?php foreach ($errors as $err): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-3"><?php echo e($err); ?></div>
  <?php endforeach; ?>
  <form method="POST" data-validate novalidate>
    <?php echo csrf_field(); ?>
    <label class="block text-sm font-medium mb-1" for="email">Email</label>
    <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required
           class="w-full border rounded-lg px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-brand">
    <label class="block text-sm font-medium mb-1" for="password">Password</label>
    <input type="password" id="password" name="password" required
           class="w-full border rounded-lg px-4 py-2 mb-6 focus:outline-none focus:ring-2 focus:ring-brand">
    <button class="w-full bg-brand text-white py-2 rounded-lg hover:bg-brand-dark transition">Login</button>
  </form>
  <p class="text-sm text-gray-500 text-center mt-6">No account? <a href="<?php echo url('/auth/register.php'); ?>" class="text-brand hover:underline">Sign up</a></p>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
