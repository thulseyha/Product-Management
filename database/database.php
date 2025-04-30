
<?php
$localhost = "localhost";
$port = 3306;
$username = "root";
$password = "";
$db_name = "stock_product";

try {
    $dsn = "mysql:host=$localhost;port=$port;dbname=$db_name";
    $db_cnn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>