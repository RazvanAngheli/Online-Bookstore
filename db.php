<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'bookstore';

// Establish the connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to retrieve all books
function get_all_books() {
    global $conn;
    $sql = "SELECT title, description, cover, price, discount_price, category FROM books";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }

    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = [
            "title" => $row['title'],
            "description" => $row['description'],
            "cover" => $row['cover'],
            "price" => (float)$row['price'],
            "discount_price" => isset($row['discount_price']) ? (float)$row['discount_price'] : null,
            "category" => $row['category']
        ];
    }
    return $books;
}

// Function to retrieve books by category
function get_books_by_category($category) {
    global $conn;
    $sql = "SELECT title, description, cover, price, discount_price, category FROM books WHERE category = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Statement Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = [
            "title" => $row['title'],
            "description" => $row['description'],
            "cover" => $row['cover'],
            "price" => (float)$row['price'],
            "discount_price" => isset($row['discount_price']) ? (float)$row['discount_price'] : null,
            "category" => $row['category']
        ];
    }

    mysqli_stmt_close($stmt);
    return $books;
}

// Function to retrieve books with category and/or title filters
function get_books_with_filters($category, $titleFilter) {
    global $conn;

    $sql = "SELECT title, description, cover, price, discount_price, category FROM books WHERE 1=1";
    $params = [];
    $types = '';

    if ($category !== 'all') {
        $sql .= " AND category = ?";
        $params[] = $category;
        $types .= 's';
    }

    if (!empty($titleFilter)) {
        $sql .= " AND title LIKE ?";
        $params[] = '%' . $titleFilter . '%';
        $types .= 's';
    }

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Statement Error: " . mysqli_error($conn));
    }

    if ($types) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = [
            "title" => $row['title'],
            "description" => $row['description'],
            "cover" => $row['cover'],
            "price" => (float)$row['price'],
            "discount_price" => isset($row['discount_price']) ? (float)$row['discount_price'] : null,
            "category" => $row['category']
        ];
    }

    mysqli_stmt_close($stmt);
    return $books;
}

// Function to retrieve a single book by title
function get_book_by_title($title) {
    global $conn;
    $sql = "SELECT title, description, cover, price, discount_price, category FROM books WHERE title = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Statement Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $title);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $book = mysqli_fetch_assoc($result);
    if ($book) {
        $book['price'] = (float)$book['price'];
        $book['discount_price'] = isset($book['discount_price']) ? (float)$book['discount_price'] : null;
    }

    mysqli_stmt_close($stmt);
    return $book;
}
?>