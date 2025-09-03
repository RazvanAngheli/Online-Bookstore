<?php
// Database connection
$host = 'localhost';
$db = 'bookstore';
$user = 'root';
$password = '';

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Load the JSON file
$jsonFile = 'books.json'; // Path to your JSON file
if (!file_exists($jsonFile)) {
    die("Error: JSON file not found.");
}

$jsonData = file_get_contents($jsonFile);
$books = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

// Prepare the SQL query for inserting data
$sql = "INSERT INTO books (title, description, cover, price, discount_price, category)
        VALUES (:title, :description, :cover, :price, :discount_price, :category)";
$stmt = $pdo->prepare($sql);

// Insert each book into the database
foreach ($books as $book) {
    $stmt->execute([
        ':title' => $book['title'],
        ':description' => $book['description'],
        ':cover' => $book['cover'],
        ':price' => $book['price'],
        ':discount_price' => $book['discount_price'] ?? NULL,
        ':category' => $book['category']
    ]);
}

echo "Successfully imported " . count($books) . " books into the database.";
?>