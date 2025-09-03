<?php
session_start(); // Start the session

// Include the database functions
require 'db.php';

// Fetch discounted books from the database
$discountedBooks = array_filter(get_all_books(), function ($book) {
    return isset($book['discount_price']) && $book['discount_price'] < $book['price'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discounted Books</title>
    <style>
    /* CSS */
    body {
        background-color: beige;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }
    h1, h2, h3 {
        color: #5D3A00;
        margin-left: 5px;
    }
    p, li {
        color: #A47C48;
        margin-left: 5px;
    }
    .book-list {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-left: 5px;
    }
    .book {
        border: 1px solid #ddd;
        padding: 5px;
        width: 200px;
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .book img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }
    footer {
        text-align: center;
        margin-top: 20px;
        padding: 10px;
        background-color: #F4F4F4;
        border-top: 1px solid #ddd;
        color: #A47C48;
    }
    a {
        text-decoration: none;
        color: #5D3A00;
        font-weight: bold;
    }
    a:hover {
        color: #A47C48;
    }
    .button-container {
        text-align: center;
        margin: 20px 0;
    }
    .button-container a {
        background-color: #5D3A00;
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
    }
    .button-container a:hover {
        background-color: #A47C48;
    }
    .login-button {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: #5D3A00;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
    }
    .login-button:hover {
        background-color: #A47C48;
    }
    </style>
</head>
<body>
    <!-- Login Button -->
    <a href="login.php" class="login-button">Login</a>

    <h1>Discounted Books</h1>

    <div class="button-container">
        <a href="index.php">Back to Homepage</a>
    </div>

    <div class="book-list">
    <?php if (count($discountedBooks) > 0): ?>
        <?php foreach ($discountedBooks as $book): ?>
            <div class='book'>
                <h3><?= htmlspecialchars($book['title']); ?></h3>
                <img src="<?= htmlspecialchars($book['cover']); ?>" alt="<?= htmlspecialchars($book['title']); ?> Cover" />
                <p><strong>Description:</strong> <?= htmlspecialchars($book['description']); ?></p>
                <p><strong>Category:</strong> 
                    <a href="category.php?category=<?= urlencode($book['category']); ?>">
                        <?= htmlspecialchars($book['category']); ?>
                    </a>
                </p>
                <p><strong>Price:</strong> 
                    <span style="text-decoration: line-through;">$<?= number_format($book['price'], 2); ?></span>
                    <strong style="color: green;">Discounted Price: $<?= number_format($book['discount_price'], 2); ?></strong>
                </p>

                <!-- Add Edit Button -->
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <p><a href="edit_book.php?title=<?= urlencode($book['title']); ?>">Edit</a></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No discounted books available.</p>
    <?php endif; ?>
    </div>

    <div class="button-container">
        <a href="index.php">Back to Homepage</a>
    </div>

    <footer>
        <p>&copy; <?= date('Y'); ?> John's Book Emporium. All rights reserved.</p>
    </footer>
</body>
</html>