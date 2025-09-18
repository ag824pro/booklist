<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $tasks = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Ошибка при получении задач: " . $e->getMessage());
}

$message = '';
$messageType = '';

if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = 'Задача успешно удалена!';
    $messageType = 'success';
} elseif (isset($_GET['status']) && $_GET['status'] == 'completed') {
    $message = 'Задача отмечена как выполненная!';
    $messageType = 'success';
} elseif (isset($_GET['status']) && $_GET['status'] == 'uncompleted') {
    $message = 'Задача отмечена как не выполненная!';
    $messageType = 'success';
} elseif (isset($_GET['error']) && $_GET['error'] == '1') {
    $message = 'Произошла ошибка при выполнении операции!';
    $messageType = 'danger';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система управления задачами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .task-card {
            transition: transform 0.2s;
        }
        .task-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.8em;
        }
        .completed {
            opacity: 0.7;
        }
        .completed .card-title {
            text-decoration: line-through;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="display-4 text-primary">
                        <i class="fas fa-tasks"></i> Система управления задачами
                    </h1>
                    <a href="add.php" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Добавить задачу
                    </a>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($tasks)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h4>Нет задач</h4>
                        <p>Начните с добавления первой задачи!</p>
                        <a href="add.php" class="btn btn-primary">Добавить задачу</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($tasks as $task): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card task-card h-100 <?php echo $task['status'] === 'выполнена' ? 'completed' : ''; ?>">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="badge <?php echo $task['status'] === 'выполнена' ? 'bg-success' : 'bg-warning'; ?> status-badge">
                                            <?php echo htmlspecialchars($task['status']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d.m.Y H:i', strtotime($task['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h5>
                                        <?php if (!empty($task['description'])): ?>
                                            <p class="card-text"><?php echo htmlspecialchars($task['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group w-100" role="group">
                                            <?php if ($task['status'] === 'не выполнена'): ?>
                                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=выполнена" 
                                                   class="btn btn-success btn-sm" 
                                                   onclick="return confirm('Отметить задачу как выполненную?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=не выполнена" 
                                                   class="btn btn-warning btn-sm" 
                                                   onclick="return confirm('Отметить задачу как не выполненную?')">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="edit.php?id=<?php echo $task['id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="delete.php?id=<?php echo $task['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
