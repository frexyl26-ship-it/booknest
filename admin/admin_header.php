<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$page_title = $page_title ?? 'Admin - BookNest';
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
<body class="bg-gray-50 text-gray-800 min-h-screen md:flex">
<aside class="bg-gray-900 text-gray-200 md:w-56 md:min-h-screen p-4 flex md:flex-col gap-1 overflow-x-auto text-sm">
  <div class="text-xl font-bold text-white px-3 py-2 whitespace-nowrap">📚 BookNest Admin</div>
  <a href="<?php echo url('/admin/index.php'); ?>" class="px-3 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">Dashboard</a>
  <a href="<?php echo url('/admin/books.php'); ?>" class="px-3 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">Books</a>
  <a href="<?php echo url('/admin/categories.php'); ?>" class="px-3 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">Categories</a>
  <a href="<?php echo url('/admin/borrowings.php'); ?>" class="px-3 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">Borrowings</a>
  <a href="<?php echo url('/admin/users.php'); ?>" class="px-3 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">Users</a>
  <a href="<?php echo url('/index.php'); ?>" class="px-3 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap md:mt-4">← Back to Site</a>
  <a href="<?php echo url('/auth/logout.php'); ?>" class="px-3 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">Logout</a>
</aside>
<main class="flex-1 p-6">
<?php show_flash(); ?>
