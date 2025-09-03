<?php
require 'auth.php'; // Include the reusable authentication logic

// Load books from books.json
$jsonData = file_get_contents('books.json');
if ($jsonData === false) {
    die("Error: Unable to load books.json. Please check if the file exists and has correct permissions.");
}

$books = json_decode($jsonData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: JSON Decode Error - " . json_last_error_msg());
}

// Get the book ID from the query string
$idToEdit = $_GET['id'] ?? '';
$bookToEdit = null;

// Find the book to edit using its ID
foreach ($books as &$book) {
    if ($book['id'] === $idToEdit) {
        $bookToEdit = &$book;
        break;
    }
}

// If the book to edit is not found, redirect back to the homepage
if (!$bookToEdit) {
    header("Location: index.php");
    exit;
}

$errors = []; // Array to store validation errors

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Server-side validation
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $price = $_POST['price'];

    if (empty($title) || strlen($title) > 100) {
        $errors[] = "The title is required and must not exceed 100 characters.";
    }

    if (empty($description) || strlen($description) > 500) {
        $errors[] = "The description is required and must not exceed 500 characters.";
    }

    if (empty($category)) {
        $errors[] = "The category is required.";
    }

    if (!is_numeric($price) || $price < 0) {
        $errors[] = "The price must be a positive number.";
    }

    // If there are no errors, update the book
    if (empty($errors)) {
        $bookToEdit['title'] = $title;
        $bookToEdit['description'] = $description;
        $bookToEdit['category'] = $category;
        $bookToEdit['price'] = (float)$price;

        // Check if a new cover image is uploaded
        if (!empty($_FILES['cover']['tmp_name'])) {
            $coverPath = 'covers/' . basename($_FILES['cover']['name']);
            move_uploaded_file($_FILES['cover']['tmp_name'], $coverPath);
            $bookToEdit['cover'] = $coverPath;
        }

        // Save the updated book data
        file_put_contents('books.json', json_encode($books, JSON_PRETTY_PRINT));
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - <?= htmlspecialchars($bookToEdit['title']); ?></title>
    <style>
        body {
            background-color: beige;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #5D3A00;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        label {
            font-size: 16px;
            color: #5D3A00;
            display: block;
            margin-bottom: 5px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background-color: #5D3A00;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #A47C48;
        }
        .back-link {
            display: inline-block;
            margin-top: 10px;
            background-color: #5D3A00;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover {
            background-color: #A47C48;
        }
        .errors {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            list-style: none;
            padding: 0;
        }
    </style>
</head>
<body>
    <h1>Edit Book - <?= htmlspecialchars($bookToEdit['title']); ?></h1>

    <!-- Display Validation Errors -->
    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="title">Title (max 100 characters):</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($bookToEdit['title']); ?>" required maxlength="100">

        <label for="description">Description (max 500 characters):</label>
        <textarea name="description" id="description" required maxlength="500"><?= htmlspecialchars($bookToEdit['description']); ?></textarea>

        <label for="category">Category:</label>
        <input type="text" name="category" id="category" value="<?= htmlspecialchars($bookToEdit['category']); ?>" required>

        <label for="price">Price (positive number):</label>
        <input type="number" name="price" id="price" step="0.01" value="<?= htmlspecialchars($bookToEdit['price']); ?>" required min="0">

        <label for="cover">Cover Image:</label>
        <input type="file" name="cover" id="cover">

        <button type="submit">Save Changes</button>
    </form>
    <a href="index.php" class="back-link">Back to Homepage</a>
</body>
</html>