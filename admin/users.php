<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$page_title = 'Users - BookNest Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['delete_id'] ?? 0);
    if ($id === (int)$_SESSION['user_id']) {
        flash('error', 'You cannot delete your own account.');
    } else {
        try {
            $db->prepare("DELETE FROM users WHERE id = ? AND role = 'member'")->execute([$id]);
            flash('success', 'Member deleted.');
        } catch (PDOException $ex) {
            flash('error', 'Cannot delete a member who has borrowing records.');
        }
    }
    redirect('/admin/users.php');
}
$users = $db->query("SELECT * FROM users ORDER BY role, name")->fetchAll();
require_once __DIR__ . '/admin_header.php';
?>
<h1 class="text-2xl font-bold mb-6">Users</h1>
<div class="bg-white rounded-xl shadow overflow-x-auto">
  <table class="w-full text-left text-sm">
    <thead class="bg-gray-100 text-gray-600"><tr><th class="p-4">Name</th><th class="p-4">Email</th><th class="p-4">Role</th><th class="p-4">Joined</th><th class="p-4">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr class="border-t">
        <td class="p-4 font-medium"><?php echo e($u['name']); ?></td>
        <td class="p-4"><?php echo e($u['email']); ?></td>
        <td class="p-4"><?php echo e(ucfirst($u['role'])); ?></td>
        <td class="p-4"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
        <td class="p-4">
          <?php if ($u['role'] === 'member'): ?>
          <form method="POST" onsubmit="return confirm('Delete this member?');">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="delete_id" value="<?php echo (int)$u['id']; ?>">
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
          <?php else: ?>—<?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
