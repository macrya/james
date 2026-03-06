<?php
// students.php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

require_once 'config.php';
require_once 'Library.php';

$library = new Library($pdo);
$message = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_student') {
    $id = trim($_POST['student_id']);
    $name = trim($_POST['full_name']);
    $course = trim($_POST['course']);
    
    if (!empty($id) && !empty($name) && !empty($course)) {
        $result = $library->addStudent($id, $name, $course);
        $message = $result['message'];
        $msgType = $result['success'] ? 'success' : 'danger';
    } else {
        $message = "All fields are required."; $msgType = 'warning';
    }
}
$students = $library->getStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">📚 Central Library</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Catalog</a>
                <a class="nav-link active" href="students.php">Students</a>
                <a class="nav-link" href="borrow.php">Circulation</a>
                <a class="nav-link" href="reports.php">Reports</a>
                <a class="nav-link text-danger" href="index.php?logout=true">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show shadow-sm">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">🎓 Register Student</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_student">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Admission Number (ADMNO)</label>
                                <input type="text" class="form-control" name="student_id" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Full Name</label>
                                <input type="text" class="form-control" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Course</label>
                                <input type="text" class="form-control" name="course" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register Student</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white">👥 Registered Students</div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr><th>ADMNO</th><th>Name</th><th>Course</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($student['student_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['course']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>