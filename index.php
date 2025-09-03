<?php
session_start(); // Start the session

// Set the store name
$storeName = "John's Book Emporium";

// Include the database functions
require 'db.php';

// Fetch all books from the database
$books = get_all_books();
if (!$books || count($books) === 0) {
    die("Error: No books available in the database.");
}

// Random "Book of the Day"
$featuredBookIndex = array_rand($books);
$featuredBook = $books[$featuredBookIndex];

// Greeting based on time
$currentTime = date('H');
if ($currentTime < 12) {
    $greeting = "Good morning";
} elseif ($currentTime < 18) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

// Filter discounted books
$discountedBooks = array_filter($books, function ($book) {
    return isset($book['discount_price']) && $book['discount_price'] < $book['price'];
});

// Select 5 random discounted books
$randomDiscountedBookKeys = array_rand($discountedBooks, min(5, count($discountedBooks)));
$displayedDiscountedBooks = [];
if (is_array($randomDiscountedBookKeys)) {
    foreach ($randomDiscountedBookKeys as $key) {
        $displayedDiscountedBooks[] = $discountedBooks[$key];
    }
} else {
    $displayedDiscountedBooks[] = $discountedBooks[$randomDiscountedBookKeys];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to <?= htmlspecialchars($storeName); ?></title>
    <style>
    body {
        background-color: beige;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .button {
        margin-right: 20px;
        padding: 8px 12px;
        background-color: #5D3A00;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
    }
    .button:hover {
        background-color: #A47C48;
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
    form {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    label {
        font-size: 16px;
        color: #5D3A00;
    }
    select, input[type="text"] {
        padding: 5px;
        font-size: 14px;
        border-radius: 5px;
        border: 1px solid #ddd;
        background-color: white;
        color: #5D3A00;
    }
    button {
        background-color: #5D3A00;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
    }
    button:hover {
        background-color: #A47C48;
    }
    .more-button {
        margin-top: 20px;
        text-align: center;
    }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= htmlspecialchars($greeting . ", welcome to " . $storeName); ?>!</h1>
        <div>
            <a href="login.php" class="button">Login</a>
        </div>
    </div>

    <h2>Choose a Category</h2>
    <form action="category.php" method="get">
        <label for="category">Choose a category:</label>
        <select name="category" id="category">
            <option value="all">Show All</option>
            <?php foreach (array_unique(array_column($books, 'category')) as $category): ?>
                <option value="<?= htmlspecialchars($category); ?>"><?= htmlspecialchars($category); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="title">Search by Title:</label>
        <input type="text" name="title" id="title" placeholder="Enter book title">
        
        <button type="submit">Filter Books</button>
    </form>

    <h2>Book of the Day</h2>
    <div class="book">
        <h3><?= htmlspecialchars($featuredBook['title']); ?></h3>
        <img src="<?= htmlspecialchars($featuredBook['cover']); ?>" alt="<?= htmlspecialchars($featuredBook['title']); ?> Cover" />
        <p><strong>Description:</strong> <?= htmlspecialchars($featuredBook['description']); ?></p>
        <p><strong>Category:</strong> 
            <a href="category.php?category=<?= urlencode($featuredBook['category']); ?>">
                <?= htmlspecialchars($featuredBook['category']); ?>
            </a>
        </p>
        <?php if (isset($featuredBook['discount_price'])): ?>
            <p><strong>Price:</strong> <span style='text-decoration: line-through;'>$<?= number_format($featuredBook['price'], 2); ?></span>
            <strong style="color: green;">Discounted Price: $<?= number_format($featuredBook['discount_price'], 2); ?></strong>
            </p>
        <?php else: ?>
            <p><strong>Price:</strong> $<?= number_format($featuredBook['price'], 2); ?></p>
        <?php endif; ?>
    </div>

    <h2>Discounted Books</h2>
<div class="book-list">
    <?php if (count($displayedDiscountedBooks) > 0): ?>
        <?php foreach ($displayedDiscountedBooks as $book): ?>
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
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No discounted books available.</p>
    <?php endif; ?>
</div>

<div class="more-button">
    <a href="discounted.php">
        <button>See More Discounted Books</button>
    </a>
</div>

    <footer>
        <p>&copy; <?= date('Y'); ?> <?= htmlspecialchars($storeName); ?>. All rights reserved.</p>
    </footer>
</body>
</html>