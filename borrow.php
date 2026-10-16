<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}
verify_csrf();

$book_id = (int)($_POST['book_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];
$max_active = 3;

try {
    $db->beginTransaction();

    $stmt = $db->prepare("SELECT id, title, copies_available FROM books WHERE id = ? FOR UPDATE");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if (!$book) {
        throw new Exception('Book not found.');
    }
    if ($book['copies_available'] < 1) {
        throw new Exception('Sorry, no copies of that book are available.');
    }

    $stmt = $db->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND status = 'borrowed'");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() >= $max_active) {
        throw new Exception("You can only borrow up to $max_active books at a time.");
    }

    $stmt = $db->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND book_id = ? AND status = 'borrowed'");
    $stmt->execute([$user_id, $book_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('You already borrowed this book.');
    }

    $due = date('Y-m-d', strtotime('+7 days'));
    $stmt = $db->prepare("INSERT INTO borrowings (user_id, book_id, due_date) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $book_id, $due]);

    $stmt = $db->prepare("UPDATE books SET copies_available = copies_available - 1 WHERE id = ?");
    $stmt->execute([$book_id]);

    $db->commit();
    flash('success', 'You borrowed "' . $book['title'] . '". Due on ' . date('M d, Y', strtotime($due)) . '.');
    redirect('/my-books.php');
} catch (Exception $ex) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    flash('error', $ex->getMessage());
    redirect('/index.php');
}
