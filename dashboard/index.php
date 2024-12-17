<?php

include 'db.php';

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php'); // Redirect to login if not authenticated
    exit;
}
// Include database connection

// Fetch data from the "products" table
try {
    $query = "SELECT * FROM products ORDER BY scrap_start_time DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="text-center">
        <h1 class="text-3xl font-bold text-gray-800">Welcome, Admin!</h1>
        <p class="mt-4 text-gray-600">You are logged in successfully.</p>
        <div class="flex justify-center gap-x-2">
            
        <a href="upload.php" 
            class="mt-6 inline-block bg-green-500 text-white font-bold py-2 px-4 rounded-md hover:bg-green-600">
            Upload
        </a>
            
        <a href="../login/logout.php" 
            class="mt-6 inline-block bg-red-500 text-white font-bold py-2 px-4 rounded-md hover:bg-red-600">
            Logout
        </a>
        </div>
    </div>
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">Product Dashboard</h1>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full bg-white border border-gray-200 shadow-md">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Site</th>
                        <th class="px-4 py-2">URL</th>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Brand</th>
                        <th class="px-4 py-2">Size</th>
                        <th class="px-4 py-2">Product Details</th>
                        <th class="px-4 py-2">Availability</th>
                        <th class="px-4 py-2">Price</th>
                        <th class="px-4 py-2">Thumbnail</th>
                        <th class="px-4 py-2">Affiliated URL</th>
                        <th class="px-4 py-2">Scrap Start Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($product['id']) ?></td>
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($product['site']) ?></td>
                                <td class="px-4 py-2 text-center">
                                    <a href="<?= htmlspecialchars($product['url']) ?>" class="text-blue-600 underline" target="_blank">Link</a>
                                </td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['title']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['brand']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['size']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['product_details']) ?></td>
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($product['availability']) ?></td>
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($product['price']) ?></td>
                                <td class="px-4 py-2 text-center">
                                    <img src="<?= htmlspecialchars($product['thumbnail_image']) ?>" alt="Thumbnail" class="w-12 h-12 mx-auto">
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="<?= htmlspecialchars($product['affiliated_url']) ?>" class="text-blue-600 underline" target="_blank">Affiliated Link</a>
                                </td>
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($product['scrap_start_time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center px-4 py-2 text-red-500">No products available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

