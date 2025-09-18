<?php
require_once 'config.php';

$bookId = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$bookId || !is_numeric($bookId) || !in_array($status, ['прочитана', 'в процессе', 'в планах'])) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    
    if (!$stmt->fetch()) {
        header('Location: index.php');
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE books SET status = ? WHERE id = ?");
    $stmt->execute([$status, $bookId]);
    
    header("Location: index.php?status=$status");
    exit;
    
} catch(PDOException $e) {
    header('Location: index.php?error=1');
    exit;
}
?>
