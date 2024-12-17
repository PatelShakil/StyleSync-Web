<?php
// File: upload_process.php

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php'); // Redirect to login if not authenticated
    exit;
}

// Database configuration
$host = "localhost";
$dbname = "stylesync";
$user = "postgres";
$password = "1095";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// File upload handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $filename = $_FILES['file']['name'];
    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

    // Only allow CSV or Excel
    if (!in_array($fileExtension, ['csv', 'xlsx', 'xls'])) {
        die("Invalid file format. Only CSV or Excel files are allowed.");
    }

    // Load the file
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Skip the header row
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];

        // Map data from the file
        $site = $row[0];
        $url = $row[1];
        $title = $row[2];
        $brand = $row[3];
        $size = $row[4];
        $productDetails = $row[5];
        $availability = $row[6];
        $price = $row[7];
        $thumbnail = $row[8];
        $affiliatedUrl = $row[9];
        $scrapStartTime = $row[10];

        try {
            // Check if the URL already exists
            $checkQuery = "SELECT id FROM products WHERE url = :url";
            $stmt = $pdo->prepare($checkQuery);
            $stmt->execute([':url' => $url]);
            $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingProduct) {
                // Update existing record
                $updateQuery = "UPDATE products SET 
                                site = :site, title = :title, brand = :brand, size = :size, 
                                product_details = :product_details, availability = :availability, 
                                price = :price, thumbnail_image = :thumbnail_image, 
                                affiliated_url = :affiliated_url, scrap_start_time = :scrap_start_time
                                WHERE url = :url";
                $stmt = $pdo->prepare($updateQuery);
                $stmt->execute([
                    ':site' => $site, ':title' => $title, ':brand' => $brand,
                    ':size' => $size, ':product_details' => $productDetails, 
                    ':availability' => $availability, ':price' => $price,
                    ':thumbnail_image' => $thumbnail, ':affiliated_url' => $affiliatedUrl,
                    ':scrap_start_time' => $scrapStartTime, ':url' => $url
                ]);
            } else {
                // Insert new record
                $insertQuery = "INSERT INTO products 
                                (site, url, title, brand, size, product_details, availability, price, 
                                thumbnail_image, affiliated_url, scrap_start_time) 
                                VALUES (:site, :url, :title, :brand, :size, :product_details, 
                                :availability, :price, :thumbnail_image, :affiliated_url, :scrap_start_time)";
                $stmt = $pdo->prepare($insertQuery);
                $stmt->execute([
                    ':site' => $site, ':url' => $url, ':title' => $title, ':brand' => $brand,
                    ':size' => $size, ':product_details' => $productDetails, 
                    ':availability' => $availability, ':price' => $price,
                    ':thumbnail_image' => $thumbnail, ':affiliated_url' => $affiliatedUrl,
                    ':scrap_start_time' => $scrapStartTime
                ]);
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    echo "File processed successfully!";
} else {
    echo "No file uploaded.";
}
?>

<!-- Simple upload form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <form action="upload.php" method="POST" enctype="multipart/form-data"
          class="p-6 bg-white shadow-md rounded-md">
        <h1 class="text-2xl font-bold mb-4 text-gray-700">Upload Product File</h1>
        <input type="file" name="file" accept=".csv,.xlsx,.xls" required 
               class="mb-4 w-full border rounded-md p-2">
        <button type="submit" 
                class="bg-blue-500 text-white font-bold px-4 py-2 rounded-md hover:bg-blue-600">
            Upload File
        </button>
    </form>
</body>
</html>
