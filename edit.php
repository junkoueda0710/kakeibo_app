<?php 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

$id = $_GET['id'] ?? null; 

 

if (!$id) { 

    header("Location: index.php"); 

    exit; 

} 

 

$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?"); 

$stmt->execute([$id]); 

$transaction = $stmt->fetch(); 

 

if (!$transaction) { 

    echo "データが見つかりません"; 

    exit; 

} 

 

$stmt = $pdo->query("SELECT * FROM categories ORDER BY type, name"); 

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 

if ($_SERVER["REQUEST_METHOD"] === "POST") { 

    $date = $_POST['date']; 

    $category_id = $_POST['category_id']; 

    $amount = $_POST['amount']; 

    $memo = $_POST['memo']; 

 

    $update = $pdo->prepare("UPDATE transactions SET date=?, category_id=?, amount=?, memo=? WHERE id=?"); 

    $update->execute([$date, $category_id, $amount, $memo, $id]); 

 

    header("Location: index.php"); 

    exit; 

} 

?> 

 

<!DOCTYPE html> 

<html lang="ja"> 

<head><meta charset="UTF-8"><title>編集</title></head> 

<body> 

    <h1>記録の編集</h1> 

    <form method="post"> 

        <label>日付：<input type="date" name="date" value="<?= $transaction['date'] ?>" required></label><br> 

        <label>カテゴリ： 

            <select name="category_id"> 

                <?php foreach ($categories as $c): ?> 

                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $transaction['category_id'] ? 'selected' : '' ?>> 

                        <?= htmlspecialchars($c['name']) ?> 

                    </option> 

                <?php endforeach; ?> 

            </select> 

        </label><br> 

        <label>金額：<input type="number" name="amount" value="<?= $transaction['amount'] ?>" required></label><br> 

        <label>メモ：<input type="text" name="memo" value="<?= htmlspecialchars($transaction['memo']) ?>"></label><br> 

        <button type="submit">更新</button> 

    </form> 

    <p><a href="index.php">← 戻る</a></p> 

</body> 

</html> 