<?php
require_once 'config.php';

$taskId = $_GET['id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    
    if (!$stmt->fetch()) {
        header('Location: index.php');
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    
    header('Location: index.php?deleted=1');
    exit;
    
} catch(PDOException $e) {
    header('Location: index.php?error=1');
    exit;
}
?>
