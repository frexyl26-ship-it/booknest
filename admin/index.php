<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$page_title = 'Dashboard - BookNest Admin';

$total_books = $db->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_copies = $db->query("SELECT COALESCE(SUM(copies_total),0) FROM books")->fetchColumn();
$active = $db->query("SELECT COUNT(*) FROM borrowings WHERE status='borrowed'")->fetchColumn();
$overdue = $db->query("SELECT COUNT(*) FROM borrowings WHERE status='borrowed' AND due_date < CURDATE()")->fetchColumn();
$members = $db->query("SELECT COUNT(*) FROM users WHERE role='member'")->fetchColumn();
$recent = $db->query("SELECT br.*, b.title, u.name FROM borrowings br
        JOIN books b ON br.book_id=b.id JOIN users u ON br.user_id=u.id
        ORDER BY br.borrowed_at DESC LIMIT 5")->fetchAll();

require_once __DIR__ . '/admin_header.php';
?>
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
  <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Book Titles</p><p class="text-3xl font-bold"><?php echo (int)$total_books; ?></p></div>
  <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Total Copies</p><p class="text-3xl font-bold"><?php echo (int)$total_copies; ?></p></div>
  <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Currently Borrowed</p><p class="text-3xl font-bold text-brand"><?php echo (int)$active; ?></p></div>
  <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Overdue</p><p class="text-3xl font-bold text-red-600"><?php echo (int)$overdue; ?></p></div>
  <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Members</p><p class="text-3xl font-bold"><?php echo (int)$members; ?></p></div>
</div>
<div class="bg-white rounded-xl shadow p-5">
  <h2 class="font-semibold mb-4">Recent Borrowings</h2>
  <?php if (!$recent): ?><p class="text-gray-500 text-sm">No borrowings yet.</p><?php else: ?>
  <table class="w-full text-sm text-left">
    <?php foreach ($recent as $r): ?>
    <tr class="border-t">
      <td class="py-2"><?php echo e($r['title']); ?></td>
      <td class="py-2 text-gray-500"><?php echo e($r['name']); ?></td>
      <td class="py-2 text-gray-400"><?php echo date('M d, Y', strtotime($r['borrowed_at'])); ?></td>
      <td class="py-2 font-medium"><?php echo e(ucfirst($r['status'])); ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
