<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$page_title = 'Borrowings - BookNest Admin';

$filter = $_GET['status'] ?? 'all';
$sql = "SELECT br.*, b.title, u.name, u.email,
        (br.status='borrowed' AND br.due_date < CURDATE()) AS overdue
        FROM borrowings br JOIN books b ON br.book_id=b.id JOIN users u ON br.user_id=u.id";
if ($filter === 'borrowed') {
    $sql .= " WHERE br.status='borrowed'";
} elseif ($filter === 'returned') {
    $sql .= " WHERE br.status='returned'";
} elseif ($filter === 'overdue') {
    $sql .= " WHERE br.status='borrowed' AND br.due_date < CURDATE()";
}
$sql .= " ORDER BY br.borrowed_at DESC";
$rows = $db->query($sql)->fetchAll();
require_once __DIR__ . '/admin_header.php';
?>
<h1 class="text-2xl font-bold mb-4">Borrowings</h1>
<div class="flex gap-2 mb-6 text-sm">
  <?php foreach (['all' => 'All', 'borrowed' => 'Borrowed', 'overdue' => 'Overdue', 'returned' => 'Returned'] as $k => $label): ?>
    <a href="?status=<?php echo $k; ?>" class="px-4 py-2 rounded-lg <?php echo $filter === $k ? 'bg-brand text-white' : 'bg-white shadow hover:bg-gray-100'; ?>"><?php echo $label; ?></a>
  <?php endforeach; ?>
</div>
<div class="bg-white rounded-xl shadow overflow-x-auto">
  <table class="w-full text-left text-sm">
    <thead class="bg-gray-100 text-gray-600"><tr><th class="p-4">Book</th><th class="p-4">Member</th><th class="p-4">Due</th><th class="p-4">Status</th><th class="p-4">Action</th></tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="5" class="p-6 text-center text-gray-500">No records.</td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?>
      <tr class="border-t <?php echo $r['overdue'] ? 'bg-red-50' : ''; ?>">
        <td class="p-4 font-medium"><?php echo e($r['title']); ?></td>
        <td class="p-4"><?php echo e($r['name']); ?><br><span class="text-xs text-gray-400"><?php echo e($r['email']); ?></span></td>
        <td class="p-4"><?php echo date('M d, Y', strtotime($r['due_date'])); ?></td>
        <td class="p-4"><?php echo $r['overdue'] ? '<span class="text-red-600 font-semibold">Overdue</span>' : e(ucfirst($r['status'])); ?></td>
        <td class="p-4">
          <?php if ($r['status'] === 'borrowed'): ?>
          <form method="POST" action="<?php echo url('/return.php'); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="borrowing_id" value="<?php echo (int)$r['id']; ?>">
            <input type="hidden" name="to" value="admin">
            <button class="bg-brand text-white px-3 py-1 rounded-lg hover:bg-brand-dark transition">Mark Returned</button>
          </form>
          <?php else: ?>—<?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
