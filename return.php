<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/my-books.php');
}
verify_csrf();

$id = (int)($_POST['borrowing_id'] ?? 0);
$from_admin = is_admin() && ($_POST['to'] ?? '') === 'admin';

try {
    $db->beginTransaction();

    if (is_admin()) {
        $stmt = $db->prepare("SELECT * FROM borrowings WHERE id = ? AND status = 'borrowed' FOR UPDATE");
        $stmt->execute([$id]);
    } else {
        $stmt = $db->prepare("SELECT * FROM borrowings WHERE id = ? AND user_id = ? AND status = 'borrowed' FOR UPDATE");
        $stmt->execute([$id, $_SESSION['user_id']]);
    }
    $row = $stmt->fetch();
    if (!$row) {
        throw new Exception('Borrowing record not found or already returned.');
    }

    $db->prepare("UPDATE borrowings SET status = 'returned', returned_at = NOW() WHERE id = ?")->execute([$id]);
    $db->prepare("UPDATE books SET copies_available = copies_available + 1 WHERE id = ?")->execute([$row['book_id']]);

    $db->commit();
    flash('success', 'Book marked as returned.');
} catch (Exception $ex) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    flash('error', $ex->getMessage());
}
redirect($from_admin ? '/admin/borrowings.php' : '/my-books.php');
