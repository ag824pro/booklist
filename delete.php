<?php
require_once 'config.php';

$bookId = $_GET['id'] ?? null;

if (!$bookId || !is_numeric($bookId)) {
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
    
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    
    header('Location: index.php?deleted=1');
    exit;
    
} catch(PDOException $e) {
    header('Location: index.php?error=1');
    exit;
}
?>
