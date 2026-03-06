<?php
// index.php
<?php
// Place this at the absolute top of index.php
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Add a logout handler at the top of index.php as well
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
require_once 'config.php';
require_once 'Library.php';

$library = new Library($pdo);
$message = '';
$msgType = ''; // Used to determine the color of the alert (success, danger, warning)

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $id = trim($_POST['book_id']);
                $title = trim($_POST['title']);
                $author = trim($_POST['author']);
                if (!empty($id) && !empty($title) && !empty($author)) {
                    $result = $library->addBook($id, $title, $author);
                    $message = $result['message'];
                    $msgType = $result['success'] ? 'success' : 'danger';
                } else {
                    $message = "All fields are required.";
                    $msgType = 'warning';
                }
                break;
            case 'borrow':
                $result = $library->toggleBorrowStatus($_POST['book_id'], 1);
                $message = $result['message'];
                $msgType = $result['success'] ? 'success' : 'danger';
                break;
            case 'return':
                $result = $library->toggleBorrowStatus($_POST['book_id'], 0);
                $message = $result['message'];
                $msgType = $result['success'] ? 'success' : 'danger';
                break;
        }
    }
}

$books = $library->getBooks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f4f9;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">📚 Central Library System</a>
        </div>
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand" href="#">📚 Central Library System</a>
            <a href="?logout=true" class="btn btn-sm btn-outline-light">Logout</a>
        </div>
    </nav>

 

    <div class="container">
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show shadow-sm" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header rounded-top">
                        ➕ Add New Book
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Book ID / ISBN</label>
                                <input type="text" class="form-control" name="book_id" placeholder="e.g., 978-3-16" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Book Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Enter title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Author Name</label>
                                <input type="text" class="form-control" name="author" placeholder="Enter author" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Book to Database</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary rounded-top">
                        📖 Current Catalog
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th class="text-end px-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($books)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No books found in the database. Add one to get started.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($books as $book): ?>
                                            <tr>
                                                <td class="px-4 align-middle"><strong><?php echo htmlspecialchars($book['book_id']); ?></strong></td>
                                                <td class="align-middle"><?php echo htmlspecialchars($book['title']); ?></td>
                                                <td class="align-middle text-muted"><?php echo htmlspecialchars($book['author']); ?></td>
                                                <td class="align-middle">
                                                    <?php if ($book['is_borrowed']): ?>
                                                        <span class="badge bg-warning text-dark">Borrowed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Available</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end px-4 align-middle">
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                                        <?php if ($book['is_borrowed']): ?>
                                                            <input type="hidden" name="action" value="return">
                                                            <button type="submit" class="btn btn-sm btn-outline-success px-3">Return</button>
                                                        <?php else: ?>
                                                            <input type="hidden" name="action" value="borrow">
                                                            <button type="submit" class="btn btn-sm btn-outline-primary px-3">Borrow</button>
                                                        <?php endif; ?>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>