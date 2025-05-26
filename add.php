<?php 

 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

if ($_SERVER["REQUEST_METHOD"] === "POST") { 

    $date = $_POST['date']; 

    $category_id = $_POST['category_id']; 

    $amount = $_POST['amount']; 

    $memo = $_POST['memo']; 

 

    $stmt = $pdo->prepare("INSERT INTO transactions (date, category_id, amount, memo) VALUES (?, ?, ?, ?)"); 

    $stmt->execute([$date, $category_id, $amount, $memo]); 

 

    header("Location: index.php"); 

    exit; 

} 

 

// カテゴリ取得（全件） 

$stmt = $pdo->query("SELECT * FROM categories ORDER BY type, name"); 

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC); 

?> 

 

<!DOCTYPE html> 

<html lang="ja"> 

<head> 

    <meta charset="UTF-8"> 

    <title>収支の追加</title> 

    <style> 

        body { font-family: sans-serif; margin: 2em; background: #fffef8; } 

        label { display: block; margin-top: 10px; } 

        input, select, textarea { width: 100%; padding: 8px; margin-top: 4px; } 

        button { margin-top: 15px; padding: 10px 20px; } 

        .hidden { display: none; } 

    </style> 

</head> 

<body> 

    <h1>収支の追加</h1> 

    <form action="" method="post"> 

        <label>日付: 

            <input type="date" name="date" value="<?= date('Y-m-d') ?>" required> 

        </label> 

 

        <label>タイプ: 

            <select id="typeSelect" required> 

                <option value="">選択してください</option> 

                <option value="支出">支出</option> 

                <option value="収入">収入</option> 

            </select> 

        </label> 

 

        <label>カテゴリ: 

            <select name="category_id" id="categorySelect" required> 

                <?php foreach ($categories as $c): ?> 

                    <option value="<?= $c['id'] ?>" data-type="<?= $c['type'] ?>"> 

                        <?= htmlspecialchars($c['name']) ?> 

                    </option> 

                <?php endforeach; ?> 

            </select> 

        </label> 

 

        <label>金額: 

            <input type="number" name="amount" required> 

        </label> 

        <label>メモ（任意）: 

            <textarea name="memo"></textarea> 

        </label> 

        <button type="submit">登録する</button> 

    </form> 

    <p><a href="index.php">← 戻る</a></p> 

 

    <script> 

        const typeSelect = document.getElementById('typeSelect'); 

        const categorySelect = document.getElementById('categorySelect'); 

        const allOptions = Array.from(categorySelect.options); 

 

        function filterCategories() { 

            const selectedType = typeSelect.value; 

            categorySelect.innerHTML = ''; 

            const filtered = allOptions.filter(opt => opt.getAttribute('data-type') === selectedType); 

            filtered.forEach(opt => categorySelect.appendChild(opt)); 

        } 

 

        typeSelect.addEventListener('change', filterCategories); 

        window.addEventListener('load', () => categorySelect.innerHTML = ''); 

    </script> 

</body> 

</html> 