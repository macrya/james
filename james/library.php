<?php
// Library.php
require_once 'config.php';

class Library {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // --- Add these methods inside your Library class ---

    // 1. Fetch all students
    public function getStudents() {
        $stmt = $this->db->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // 2. Add a new student
    public function addStudent($student_id, $full_name, $course) {
        try {
            $stmt = $this->db->prepare("INSERT INTO students (student_id, full_name, course) VALUES (?, ?, ?)");
            $stmt->execute([$student_id, $full_name, $course]);
            return ["success" => true, "message" => "Student registered successfully."];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["success" => false, "message" => "Error: Admission Number already exists."];
            }
            return ["success" => false, "message" => "Database error occurred."];
        }
    }

    // 3. Upgraded Borrow Logic (Now links to a student)
    public function borrowBook($book_id, $student_id) {
        // First, check if book is available
        $stmt = $this->db->prepare("SELECT is_borrowed FROM books WHERE book_id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();

        if ($book && $book['is_borrowed'] == 0) {
            // Update book status
            $updateStmt = $this->db->prepare("UPDATE books SET is_borrowed = 1 WHERE book_id = ?");
            $updateStmt->execute([$book_id]);

            // Log the activity
            $logStmt = $this->db->prepare("INSERT INTO activity_log (book_id, student_id, action) VALUES (?, ?, 'Borrowed')");
            $logStmt->execute([$book_id, $student_id]);

            return ["success" => true, "message" => "Book successfully borrowed by student $student_id."];
        }
        return ["success" => false, "message" => "Book is not available or does not exist."];
    }

    public function getBooks() {
        $stmt = $this->db->query("SELECT * FROM books ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function addBook($book_id, $title, $author) {
        try {
            $stmt = $this->db->prepare("INSERT INTO books (book_id, title, author) VALUES (?, ?, ?)");
            $stmt->execute([$book_id, $title, $author]);
            return ["success" => true, "message" => "Book added successfully."];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (Duplicate ID)
                return ["success" => false, "message" => "Error: Book ID already exists."];
            }
            return ["success" => false, "message" => "Database error occurred."];
        }
    }

    public function toggleBorrowStatus($book_id, $borrow_status) {
        // $borrow_status should be 1 (borrow) or 0 (return)
        $stmt = $this->db->prepare("UPDATE books SET is_borrowed = ? WHERE book_id = ?");
        $stmt->execute([$borrow_status, $book_id]);
        
        if ($stmt->rowCount() > 0) {
            $action = $borrow_status == 1 ? "borrowed" : "returned";
            return ["success" => true, "message" => "Book successfully $action."];
        }
        return ["success" => false, "message" => "Action failed. Book not found or status unchanged."];
    }
}
?>