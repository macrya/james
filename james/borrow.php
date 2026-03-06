<?php
// borrow.php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

require_once 'config.php';
require_once 'Library.php';

$library = new Library($pdo);
$message = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = trim($_POST['book_id']);
    $student_id = trim($_POST['student_id']);
    $action = $_POST['action'];

    if (!empty($book_id) && !empty($student_id)) {
        if ($action === 'borrow') {
            $result = $library->borrowBook($book_id, $student_id);
        } elseif ($action === 'return') {
            $result = $library->returnBook($book_id, $student_id);
        }
        $message = $result['message'];
        $msgType = $result['success'] ? 'success' : 'danger';
    } else {
        $message = "Please provide both Book ID and Student ADMNO."; $msgType = 'warning';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Circulation - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">📚 Central Library</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Catalog</a>
                <a class="nav-link" href="students.php">Students</a>
                <a class="nav-link active" href="borrow.php">Circulation</a>
                <a class="nav-link" href="reports.php">Reports</a>
                <a class="nav-link text-danger" href="index.php?logout=true">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container" style="max-width: 600px;">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show shadow-sm">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">🔄 Issue or Return Book</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Book ID / ISBN</label>
                        <input type="text" class="form-control" name="book_id" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Student ADMNO</label>
                        <input type="text" class="form-control" name="student_id" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="borrow" class="btn btn-primary w-50">Issue Book</button>
                        <button type="submit" name="action" value="return" class="btn btn-outline-secondary w-50">Return Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>