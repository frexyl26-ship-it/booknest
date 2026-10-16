<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$book = null;
if ($id) {
    $s = $db->prepare("SELECT * FROM books WHERE id = ?");
    $s->execute([$id]);
    $book = $s->fetch();
    if (!$book) {
        flash('error', 'Book not found.');
        redirect('/admin/books.php');
    }
}
$cats = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$errors = [];
$f = [
    'title' => $book['title'] ?? '',
    'author' => $book['author'] ?? '',
    'isbn' => $book['isbn'] ?? '',
    'category_id' => $book['category_id'] ?? '',
    'copies_total' => $book['copies_total'] ?? 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $f['title'] = trim($_POST['title'] ?? '');
    $f['author'] = trim($_POST['author'] ?? '');
    $f['isbn'] = trim($_POST['isbn'] ?? '');
    $f['category_id'] = (int)($_POST['category_id'] ?? 0);
    $f['copies_total'] = (int)($_POST['copies_total'] ?? 0);

    if ($f['title'] === '' || strlen($f['title']) > 200) $errors[] = 'Title is required (max 200 characters).';
    if ($f['author'] === '' || strlen($f['author']) > 150) $errors[] = 'Author is required (max 150 characters).';
    if ($f['isbn'] !== '' && !preg_match('/^[0-9Xx-]{10,20}$/', $f['isbn'])) $errors[] = 'ISBN must be 10 to 20 digits (dashes allowed).';
    if ($f['copies_total'] < 1 || $f['copies_total'] > 1000) $errors[] = 'Copies must be between 1 and 1000.';

    if (!$errors) {
        $isbn = $f['isbn'] === '' ? null : $f['isbn'];
        $cat = $f['category_id'] > 0 ? $f['category_id'] : null;
        try {
            if ($book) {
                $out = $book['copies_total'] - $book['copies_available'];
                if ($f['copies_total'] < $out) {
                    $errors[] = "Total copies cannot be less than the $out currently borrowed.";
                } else {
                    $avail = $f['copies_total'] - $out;
                    $db->prepare("UPDATE books SET title=?, author=?, isbn=?, category_id=?, copies_total=?, copies_available=? WHERE id=?")
                       ->execute([$f['title'], $f['author'], $isbn, $cat, $f['copies_total'], $avail, $id]);
                }
            } else {
                $db->prepare("INSERT INTO books (title, author, isbn, category_id, copies_total, copies_available) VALUES (?,?,?,?,?,?)")
                   ->execute([$f['title'], $f['author'], $isbn, $cat, $f['copies_total'], $f['copies_total']]);
            }
        } catch (PDOException $ex) {
            $errors[] = $ex->getCode() == 23000 ? 'That ISBN already exists.' : 'Database error. Please try again.';
        }
        if (!$errors) {
            flash('success', $book ? 'Book updated.' : 'Book added.');
            redirect('/admin/books.php');
        }
    }
}
$page_title = ($book ? 'Edit' : 'Add') . ' Book - BookNest Admin';
require_once __DIR__ . '/admin_header.php';
?>
<h1 class="text-2xl font-bold mb-6"><?php echo $book ? 'Edit Book' : 'Add Book'; ?></h1>
<div class="bg-white rounded-xl shadow p-6 max-w-xl">
  <?php foreach ($errors as $err): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-3"><?php echo e($err); ?></div>
  <?php endforeach; ?>
  <form method="POST" data-validate novalidate>
    <?php echo csrf_field(); ?>
    <label class="block text-sm font-medium mb-1">Title</label>
    <input type="text" name="title" value="<?php echo e($f['title']); ?>" required maxlength="200" class="w-full border rounded-lg px-4 py-2 mb-4">
    <label class="block text-sm font-medium mb-1">Author</label>
    <input type="text" name="author" value="<?php echo e($f['author']); ?>" required maxlength="150" class="w-full border rounded-lg px-4 py-2 mb-4">
    <label class="block text-sm font-medium mb-1">ISBN (optional)</label>
    <input type="text" name="isbn" value="<?php echo e($f['isbn']); ?>" maxlength="20" class="w-full border rounded-lg px-4 py-2 mb-4">
    <label class="block text-sm font-medium mb-1">Category</label>
    <select name="category_id" class="w-full border rounded-lg px-4 py-2 mb-4">
      <option value="0">— None —</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?php echo $c['id']; ?>" <?php echo (int)$f['category_id'] === (int)$c['id'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
      <?php endforeach; ?>
    </select>
    <label class="block text-sm font-medium mb-1">Total copies</label>
    <input type="number" name="copies_total" value="<?php echo (int)$f['copies_total']; ?>" min="1" max="1000" required class="w-full border rounded-lg px-4 py-2 mb-6">
    <div class="flex gap-3">
      <button class="bg-brand text-white px-6 py-2 rounded-lg hover:bg-brand-dark transition">Save</button>
      <a href="<?php echo url('/admin/books.php'); ?>" class="bg-gray-100 px-6 py-2 rounded-lg hover:bg-gray-200 transition">Cancel</a>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
