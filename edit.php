<?php
require_once 'config.php';

$message = '';
$messageType = '';
$task = null;

$taskId = $_GET['id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch();
    
    if (!$task) {
        header('Location: index.php');
        exit;
    }
} catch(PDOException $e) {
    die("Ошибка при получении задачи: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? '';
    

    if (empty($title)) {
        $message = 'Название задачи обязательно для заполнения!';
        $messageType = 'danger';
    } elseif (strlen($title) > 255) {
        $message = 'Название задачи не должно превышать 255 символов!';
        $messageType = 'danger';
    } elseif (!in_array($status, ['не выполнена', 'выполнена'])) {
        $message = 'Неверный статус задачи!';
        $messageType = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $description, $status, $taskId]);
            
            $message = 'Задача успешно обновлена!';
            $messageType = 'success';
            
        
            $task['title'] = $title;
            $task['description'] = $description;
            $task['status'] = $status;
        } catch(PDOException $e) {
            $message = 'Ошибка при обновлении задачи: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать задачу - Система управления задачами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Редактировать задачу
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading"></i> Название задачи <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       value="<?php echo htmlspecialchars($task['title']); ?>"
                                       maxlength="255" 
                                       required 
                                       placeholder="Введите название задачи">
                                <div class="form-text">Максимум 255 символов</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Описание задачи
                                </label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Введите подробное описание задачи (необязательно)"><?php echo htmlspecialchars($task['description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-flag"></i> Статус задачи <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="не выполнена" <?php echo $task['status'] === 'не выполнена' ? 'selected' : ''; ?>>
                                        Не выполнена
                                    </option>
                                    <option value="выполнена" <?php echo $task['status'] === 'выполнена' ? 'selected' : ''; ?>>
                                        Выполнена
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    Создано: <?php echo date('d.m.Y H:i', strtotime($task['created_at'])); ?>
                                </small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left"></i> Назад к списку
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Сохранить изменения
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
