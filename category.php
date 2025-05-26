<?php 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

if ($_SERVER["REQUEST_METHOD"] === "POST") { 

    $name = $_POST['name']; 

    $type = $_POST['type']; 

 

    if ($name && in_array($type, ['収入', '支出'])) { 

        $stmt = $pdo->prepare("INSERT INTO categories (name, type) VALUES (?, ?)"); 

        $stmt->execute([$name, $type]); 

    } 

 

    header("Location: category.php"); 

    exit; 

} 

 

$stmt = $pdo->query("SELECT * FROM categories ORDER BY type, name"); 

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC); 

?> 

 

<!DOCTYPE html> 

<html lang="ja"> 

<head> 

    <meta charset="UTF-8"> 

    <title>カテゴリ管理</title> 

    <style> 

        body { font-family: sans-serif; margin: 2em; background: #f0f8ff; } 

        table { width: 100%; border-collapse: collapse; margin-top: 1em; } 

        th, td { padding: 8px; border: 1px solid #ccc; } 

        label { display: block; margin-top: 10px; } 

        input, select { padding: 6px; width: 100%; } 

        button { margin-top: 10px; padding: 8px 16px; } 

    </style> 

</head> 

<body> 

    <h1>カテゴリの追加</h1> 

    <form action="" method="post"> 

        <label>カテゴリ名: 

            <input type="text" name="name" required> 

        </label> 

        <label>タイプ: 

            <select name="type" required> 

                <option value="支出">支出</option> 

                <option value="収入">収入</option> 

            </select> 

        </label> 

        <button type="submit">追加</button> 

    </form> 

 

    <h2>登録済みカテゴリ</h2> 

    <table> 

        <tr> 

            <th>カテゴリ名</th> 

            <th>タイプ</th> 

        </tr> 

        <?php foreach ($categories as $c): ?> 

            <tr> 

                <td><?= htmlspecialchars($c['name']) ?></td> 

                <td><?= htmlspecialchars($c['type']) ?></td> 

            </tr> 

        <?php endforeach; ?> 

    </table> 

 

    <p><a href="index.php">← 家計簿トップへ戻る</a></p> 

</body> 

</html> 