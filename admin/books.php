<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$page_title = 'Books - BookNest Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['delete_id'] ?? 0);
    try {
        $db->prepare("DELETE FROM books WHERE id = ?")->execute([$id]);
        flash('success', 'Book deleted.');
    } catch (PDOException $ex) {
        flash('error', 'Cannot delete a book that has borrowing records.');
    }
    redirect('/admin/books.php');
}

$books = $db->query("SELECT b.*, c.name AS category FROM books b
        LEFT JOIN categories c ON b.category_id=c.id ORDER BY b.title")->fetchAll();
require_once __DIR__ . '/admin_header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold">Books</h1>
  <a href="<?php echo url('/admin/book_form.php'); ?>" class="bg-brand text-white px-4 py-2 rounded-lg hover:bg-brand-dark transition">+ Add Book</a>
</div>
<div class="bg-white rounded-xl shadow overflow-x-auto">
  <table class="w-full text-left text-sm">
    <thead class="bg-gray-100 text-gray-600">
      <tr><th class="p-4">Title</th><th class="p-4">Author</th><th class="p-4">Category</th><th class="p-4">Available</th><th class="p-4">Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($books as $b): ?>
      <tr class="border-t">
        <td class="p-4 font-medium"><?php echo e($b['title']); ?></td>
        <td class="p-4"><?php echo e($b['author']); ?></td>
        <td class="p-4"><?php echo e($b['category'] ?? '—'); ?></td>
        <td class="p-4"><?php echo (int)$b['copies_available']; ?> / <?php echo (int)$b['copies_total']; ?></td>
        <td class="p-4">
          <div class="flex gap-3">
            <a href="<?php echo url('/admin/book_form.php?id=' . (int)$b['id']); ?>" class="text-blue-600 hover:underline">Edit</a>
            <form method="POST" onsubmit="return confirm('Delete this book?');">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="delete_id" value="<?php echo (int)$b['id']; ?>">
              <button class="text-red-600 hover:underline">Delete</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
