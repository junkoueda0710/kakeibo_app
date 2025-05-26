<?php 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

$id = $_GET['id'] ?? null; 

 

if ($id) { 

    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?"); 

    $stmt->execute([$id]); 

} 

 

header("Location: index.php"); 

exit; 