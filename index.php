<?php
$page_title = 'Library Catalog - BookNest';
require_once __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['category'] ?? 0);

$sql = "SELECT b.*, c.name AS category FROM books b
        LEFT JOIN categories c ON b.category_id = c.id WHERE 1=1";
$params = [];
if ($q !== '') {
    $sql .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
    $like = "%$q%";
    array_push($params, $like, $like, $like);
}
if ($cat > 0) {
    $sql .= " AND b.category_id = ?";
    $params[] = $cat;
}
$sql .= " ORDER BY b.title";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();
$cats = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<div class="mb-8">
  <h1 class="text-3xl font-bold mb-1">Library Catalog</h1>
  <p class="text-gray-500">Browse, search and borrow books. Loans are for 7 days.</p>
</div>

<form method="GET" class="flex flex-col md:flex-row gap-3 mb-8">
  <input type="text" name="q" id="book-search" value="<?php echo e($q); ?>" placeholder="Search title, author or ISBN..."
         class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand">
  <select name="category" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand">
    <option value="0">All categories</option>
    <?php foreach ($cats as $c): ?>
      <option value="<?php echo $c['id']; ?>" <?php echo $cat === (int)$c['id'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <button class="bg-brand text-white px-6 py-2 rounded-lg hover:bg-brand-dark transition">Search</button>
</form>

<?php if (!$books): ?>
  <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">No books found.</div>
<?php else: ?>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach ($books as $b): ?>
  <div class="book-card bg-white rounded-xl shadow p-5 flex flex-col">
    <span class="text-xs text-brand font-semibold uppercase"><?php echo e($b['category'] ?? 'Uncategorized'); ?></span>
    <h2 class="text-lg font-bold mt-1"><?php echo e($b['title']); ?></h2>
    <p class="text-sm text-gray-500 mb-3">by <?php echo e($b['author']); ?></p>
    <p class="text-xs text-gray-400 mb-4">ISBN: <?php echo e($b['isbn'] ?? 'N/A'); ?></p>
    <div class="mt-auto flex items-center justify-between">
      <?php if ($b['copies_available'] > 0): ?>
        <span class="text-sm text-green-600 font-medium"><?php echo (int)$b['copies_available']; ?> of <?php echo (int)$b['copies_total']; ?> available</span>
      <?php else: ?>
        <span class="text-sm text-red-600 font-medium">All copies borrowed</span>
      <?php endif; ?>
      <?php if (!is_logged_in()): ?>
        <a href="<?php echo url('/auth/login.php'); ?>" class="text-sm text-brand hover:underline">Login to borrow</a>
      <?php elseif ($b['copies_available'] > 0): ?>
        <form method="POST" action="<?php echo url('/borrow.php'); ?>">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="book_id" value="<?php echo (int)$b['id']; ?>">
          <button class="bg-brand text-white text-sm px-4 py-2 rounded-lg hover:bg-brand-dark transition">Borrow</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
