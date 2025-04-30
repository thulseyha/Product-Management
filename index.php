<?php
require "./database/database.php";

// Handle all CRUD operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_id'])) {
        // Delete operation
        $query = "DELETE FROM product_tb WHERE id = :id";
        $stmt = $db_cnn->prepare($query);
        $stmt->bindParam(':id', $_POST['delete_id']);
        $stmt->execute();

        // Redirect after delete
        header("Location: index.php");
        exit();
    } else {
        // Create/Update operation
        $productName = $_POST['productName'];
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);

        if (isset($_POST['id'])) {
            // Update existing product
            $query = "UPDATE product_tb SET productName = :productName, price = :price, stock_quantity = :stock WHERE id = :id";
            $stmt = $db_cnn->prepare($query);
            $stmt->bindParam(':id', $_POST['id']);
        } else {
            // Create new product
            $query = "INSERT INTO product_tb (productName, price, stock_quantity) VALUES (:productName, :price, :stock)";
            $stmt = $db_cnn->prepare($query);
        }

        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->execute();

        // Redirect after insert/update
        header("Location: index.php");
        exit();
    }
}

// Fetch all products
$productsFromDB = [];
$sql = "SELECT id, productName, price, stock_quantity FROM product_tb ORDER BY id DESC";
$stmt = $db_cnn->query($sql);
$productsFromDB = $stmt->fetchAll();

// Get product for editing
$editProduct = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $query = "SELECT * FROM product_tb WHERE id = :id";
    $stmt = $db_cnn->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $editProduct = $stmt->fetch();
}

function getStockStatus($stock)
{
    if ($stock > 10) return "In Stock";
    if ($stock > 0) return "Low Stock";
    return "No Stock";
}

function calculateTotalPrice($products)
{
    $total = 0;
    foreach ($products as $item) {
        $total += $item['price'] * $item['stock_quantity'];
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Product Inventory Management</title>
    <style>
        body {
            background: linear-gradient(to right, #d9a7c7, #fffcdc);
            font-family: 'Arial', sans-serif;
        }

        .card-container {
            margin: 30px auto;
            max-width: 900px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn {
            transition: all 0.3s ease-in-out;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1 class="text-dark my-4">✨ Product Inventory Management ✨</h1>

        <div class="card-container">
            <!-- Add/Edit Form -->
            <div class="card p-4">
                <h5 class="mb-3"><?= $editProduct ? 'Edit' : 'Add' ?> Product</h5>
                <form method="post">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="productName" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save Product</button>
                </form>
            </div>

            <!-- Product List -->
            <div class="card p-4 mt-4">
                <h5>Product List</h5>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productsFromDB as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['productName']) ?></td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['stock_quantity'] ?></td>
                                <td><?= getStockStatus($product['stock_quantity']) ?></td>
                                <td>
                                    <a href="index.php?action=edit&id=<?= $product['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>