<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
$page_title = 'My Books - BookNest';

$stmt = $db->prepare("SELECT br.*, b.title, b.author,
        (br.status = 'borrowed' AND br.due_date < CURDATE()) AS overdue
        FROM borrowings br JOIN books b ON br.book_id = b.id
        WHERE br.user_id = ? ORDER BY br.status ASC, br.borrowed_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<h1 class="text-2xl font-bold mb-6">My Borrowed Books</h1>

<?php if (!$rows): ?>
  <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
    You haven't borrowed any books yet. <a href="<?php echo url('/index.php'); ?>" class="text-brand hover:underline">Browse the catalog</a>.
  </div>
<?php else: ?>
<div class="bg-white rounded-xl shadow overflow-x-auto">
  <table class="w-full text-left text-sm">
    <thead class="bg-gray-100 text-gray-600">
      <tr><th class="p-4">Book</th><th class="p-4">Borrowed</th><th class="p-4">Due</th><th class="p-4">Status</th><th class="p-4">Action</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr class="border-t <?php echo $r['overdue'] ? 'bg-red-50' : ''; ?>">
        <td class="p-4"><span class="font-medium"><?php echo e($r['title']); ?></span><br><span class="text-xs text-gray-400"><?php echo e($r['author']); ?></span></td>
        <td class="p-4"><?php echo date('M d, Y', strtotime($r['borrowed_at'])); ?></td>
        <td class="p-4"><?php echo date('M d, Y', strtotime($r['due_date'])); ?></td>
        <td class="p-4">
          <?php if ($r['status'] === 'returned'): ?>
            <span class="text-green-600 font-medium">Returned</span>
          <?php elseif ($r['overdue']): ?>
            <span class="text-red-600 font-semibold">Overdue</span>
          <?php else: ?>
            <span class="text-amber-600 font-medium">Borrowed</span>
          <?php endif; ?>
        </td>
        <td class="p-4">
          <?php if ($r['status'] === 'borrowed'): ?>
          <form method="POST" action="<?php echo url('/return.php'); ?>" onsubmit="return confirm('Return this book?');">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="borrowing_id" value="<?php echo (int)$r['id']; ?>">
            <button class="bg-brand text-white px-3 py-1 rounded-lg hover:bg-brand-dark transition">Return</button>
          </form>
          <?php else: ?>—<?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
