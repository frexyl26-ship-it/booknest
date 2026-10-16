<?php
require_once __DIR__ . '/functions.php';
$page_title = $page_title ?? 'BookNest Library';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($page_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#d97706', dark: '#b45309', light: '#fef3c7' } } } } }
</script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
<nav class="bg-white shadow sticky top-0 z-50">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex justify-between items-center h-16">
      <a href="<?php echo url('/index.php'); ?>" class="text-2xl font-bold text-brand">📚 BookNest</a>
      <div class="flex items-center gap-4 text-sm md:text-base">
        <a href="<?php echo url('/index.php'); ?>" class="hover:text-brand transition">Catalog</a>
        <?php if (is_logged_in()): ?>
          <a href="<?php echo url('/my-books.php'); ?>" class="hover:text-brand transition">My Books</a>
          <?php if (is_admin()): ?>
            <a href="<?php echo url('/admin/index.php'); ?>" class="hover:text-brand transition">Admin</a>
          <?php endif; ?>
          <span class="hidden md:inline text-gray-500">Hi, <?php echo e($_SESSION['name']); ?></span>
          <a href="<?php echo url('/auth/logout.php'); ?>" class="bg-gray-100 px-3 py-2 rounded-lg hover:bg-gray-200 transition">Logout</a>
        <?php else: ?>
          <a href="<?php echo url('/auth/login.php'); ?>" class="hover:text-brand transition">Login</a>
          <a href="<?php echo url('/auth/register.php'); ?>" class="bg-brand text-white px-3 py-2 rounded-lg hover:bg-brand-dark transition">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="flex-grow max-w-6xl mx-auto w-full px-4 py-8">
<?php show_flash(); ?>
