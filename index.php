<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
    $books = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Ошибка при получении книг: " . $e->getMessage());
}

$message = '';
$messageType = '';

if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = 'Книга успешно удалена!';
    $messageType = 'success';
} elseif (isset($_GET['status']) && $_GET['status'] == 'прочитана') {
    $message = 'Книга отмечена как прочитанная!';
    $messageType = 'success';
} elseif (isset($_GET['status']) && $_GET['status'] == 'в процессе') {
    $message = 'Книга отмечена как читаемая!';
    $messageType = 'success';
} elseif (isset($_GET['status']) && $_GET['status'] == 'в планах') {
    $message = 'Книга добавлена в планы!';
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
    <title>Система управления книгами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .book-card {
            transition: transform 0.2s;
        }
        .book-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.8em;
        }
        .read {
            opacity: 0.7;
        }
        .read .card-title {
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
                        <i class="fas fa-book"></i> Система управления книгами
                    </h1>
                    <a href="add.php" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Добавить книгу
                    </a>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($books)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h4>Нет книг</h4>
                        <p>Начните с добавления первой книги!</p>
                        <a href="add.php" class="btn btn-primary">Добавить книгу</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($books as $book): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card book-card h-100 <?php echo $book['status'] === 'прочитана' ? 'read' : ''; ?>">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="badge <?php 
                                            echo $book['status'] === 'прочитана' ? 'bg-success' : 
                                                ($book['status'] === 'в процессе' ? 'bg-warning' : 'bg-info'); 
                                        ?> status-badge">
                                            <?php echo htmlspecialchars($book['status']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d.m.Y H:i', strtotime($book['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                        <p class="card-text">
                                            <strong>Автор:</strong> <?php echo htmlspecialchars($book['author']); ?><br>
                                            <strong>Год:</strong> <?php echo htmlspecialchars($book['year']); ?>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group w-100" role="group">
                                            <?php if ($book['status'] === 'в планах'): ?>
                                                <a href="update_status.php?id=<?php echo $book['id']; ?>&status=в процессе" 
                                                   class="btn btn-warning btn-sm" 
                                                   onclick="return confirm('Начать читать эту книгу?')">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                            <?php elseif ($book['status'] === 'в процессе'): ?>
                                                <a href="update_status.php?id=<?php echo $book['id']; ?>&status=прочитана" 
                                                   class="btn btn-success btn-sm" 
                                                   onclick="return confirm('Отметить книгу как прочитанную?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="update_status.php?id=<?php echo $book['id']; ?>&status=в процессе" 
                                                   class="btn btn-warning btn-sm" 
                                                   onclick="return confirm('Вернуть книгу в процесс чтения?')">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="edit.php?id=<?php echo $book['id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="delete.php?id=<?php echo $book['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Вы уверены, что хотите удалить эту книгу?')">
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
