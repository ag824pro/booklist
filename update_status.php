<?php
require_once 'config.php';

$taskId = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$taskId || !is_numeric($taskId) || !in_array($status, ['не выполнена', 'выполнена'])) {
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
    

    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->execute([$status, $taskId]);
    
    $message = $status === 'выполнена' ? 'completed' : 'uncompleted';
    header("Location: index.php?status=$message");
    exit;
    
} catch(PDOException $e) {
    header('Location: index.php?error=1');
    exit;
}
?>
