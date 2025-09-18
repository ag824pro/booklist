<?php
require_once 'config.php';

$message = '';
$messageType = '';
$book = null;

$bookId = $_GET['id'] ?? null;

if (!$bookId || !is_numeric($bookId)) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        header('Location: index.php');
        exit;
    }
} catch(PDOException $e) {
    die("Ошибка при получении книги: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $status = $_POST['status'] ?? '';
    
    if (empty($title)) {
        $message = 'Название книги обязательно для заполнения!';
        $messageType = 'danger';
    } elseif (empty($author)) {
        $message = 'Автор книги обязателен для заполнения!';
        $messageType = 'danger';
    } elseif (strlen($title) > 255) {
        $message = 'Название книги не должно превышать 255 символов!';
        $messageType = 'danger';
    } elseif (strlen($author) > 255) {
        $message = 'Имя автора не должно превышать 255 символов!';
        $messageType = 'danger';
    } elseif (!empty($year) && (!is_numeric($year) || $year < 1000 || $year > date('Y'))) {
        $message = 'Год издания должен быть числом от 1000 до ' . date('Y') . '!';
        $messageType = 'danger';
    } elseif (!in_array($status, ['прочитана', 'в процессе', 'в планах'])) {
        $message = 'Неверный статус книги!';
        $messageType = 'danger';
    } else {
        try {
            $year = !empty($year) ? (int)$year : null;
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, year = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $author, $year, $status, $bookId]);
            
            $message = 'Книга успешно обновлена!';
            $messageType = 'success';
            
            $book['title'] = $title;
            $book['author'] = $author;
            $book['year'] = $year;
            $book['status'] = $status;
        } catch(PDOException $e) {
            $message = 'Ошибка при обновлении книги: ' . $e->getMessage();
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
    <title>Редактировать книгу - Система управления книгами</title>
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
                            <i class="fas fa-book"></i> Редактировать книгу
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
                                    <i class="fas fa-book"></i> Название книги <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       value="<?php echo htmlspecialchars($book['title']); ?>"
                                       maxlength="255" 
                                       required 
                                       placeholder="Введите название книги">
                                <div class="form-text">Максимум 255 символов</div>
                            </div>

                            <div class="mb-3">
                                <label for="author" class="form-label">
                                    <i class="fas fa-user"></i> Автор <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="author" 
                                       name="author" 
                                       value="<?php echo htmlspecialchars($book['author']); ?>"
                                       maxlength="255" 
                                       required 
                                       placeholder="Введите имя автора">
                                <div class="form-text">Максимум 255 символов</div>
                            </div>

                            <div class="mb-3">
                                <label for="year" class="form-label">
                                    <i class="fas fa-calendar"></i> Год издания
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="year" 
                                       name="year" 
                                       value="<?php echo htmlspecialchars($book['year']); ?>"
                                       min="1000" 
                                       max="<?php echo date('Y'); ?>"
                                       placeholder="Введите год издания (необязательно)">
                                <div class="form-text">От 1000 до <?php echo date('Y'); ?></div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-flag"></i> Статус чтения <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="в планах" <?php echo $book['status'] === 'в планах' ? 'selected' : ''; ?>>
                                        В планах
                                    </option>
                                    <option value="в процессе" <?php echo $book['status'] === 'в процессе' ? 'selected' : ''; ?>>
                                        В процессе
                                    </option>
                                    <option value="прочитана" <?php echo $book['status'] === 'прочитана' ? 'selected' : ''; ?>>
                                        Прочитана
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    Добавлено: <?php echo date('d.m.Y H:i', strtotime($book['created_at'])); ?>
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
