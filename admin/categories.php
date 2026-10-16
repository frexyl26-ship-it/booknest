<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$page_title = 'Categories - BookNest Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($action === 'add' || $action === 'rename') {
            if ($name === '' || strlen($name) > 100) {
                flash('error', 'Category name is required (max 100 characters).');
            } elseif ($action === 'add') {
                $db->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
                flash('success', 'Category added.');
            } else {
                $db->prepare("UPDATE categories SET name = ? WHERE id = ?")->execute([$name, $id]);
                flash('success', 'Category updated.');
            }
        } elseif ($action === 'delete') {
            $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
            flash('success', 'Category deleted.');
        }
    } catch (PDOException $ex) {
        flash('error', $ex->getCode() == 23000 ? 'That category already exists.' : 'Database error.');
    }
    redirect('/admin/categories.php');
}

$cats = $db->query("SELECT c.*, (SELECT COUNT(*) FROM books b WHERE b.category_id=c.id) AS book_count
        FROM categories c ORDER BY c.name")->fetchAll();
require_once __DIR__ . '/admin_header.php';
?>
<h1 class="text-2xl font-bold mb-6">Categories</h1>
<form method="POST" class="flex gap-3 mb-6 max-w-xl">
  <?php echo csrf_field(); ?>
  <input type="hidden" name="action" value="add">
  <input type="text" name="name" placeholder="New category name" required maxlength="100" class="flex-1 border rounded-lg px-4 py-2">
  <button class="bg-brand text-white px-4 py-2 rounded-lg hover:bg-brand-dark transition">Add</button>
</form>
<div class="bg-white rounded-xl shadow overflow-x-auto max-w-2xl">
  <table class="w-full text-left text-sm">
    <thead class="bg-gray-100 text-gray-600"><tr><th class="p-4">Name</th><th class="p-4">Books</th><th class="p-4">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($cats as $c): ?>
      <tr class="border-t">
        <td class="p-4">
          <form method="POST" class="flex gap-2">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="rename">
            <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
            <input type="text" name="name" value="<?php echo e($c['name']); ?>" required maxlength="100" class="border rounded-lg px-3 py-1">
            <button class="text-blue-600 hover:underline">Save</button>
          </form>
        </td>
        <td class="p-4"><?php echo (int)$c['book_count']; ?></td>
        <td class="p-4">
          <form method="POST" onsubmit="return confirm('Delete this category? Its books will become uncategorized.');">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
