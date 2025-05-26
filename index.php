<?php 

 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

// 年月の取得 

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y'); 

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); 

$start = "$year-$month-01"; 

$end = date("Y-m-t", strtotime($start)); 

 

// データ取得 

$stmt = $pdo->prepare(" 

    SELECT t.*, c.name AS category_name, c.type  

    FROM transactions t 

    JOIN categories c ON t.category_id = c.id 

    WHERE t.date BETWEEN ? AND ? 

    ORDER BY t.date DESC 

"); 

$stmt->execute([$start, $end]); 

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 

// 合計計算 

$income = 0; 

$expense = 0; 

foreach ($transactions as $t) { 

    if ($t['type'] === '収入') { 

        $income += $t['amount']; 

    } else { 

        $expense += $t['amount']; 

    } 

} 

?> 

 

<!DOCTYPE html> 

<html lang="ja"> 

<head> 

    <meta charset="UTF-8"> 

    <title>家計簿</title> 

    <style> 

        body { font-family: sans-serif; margin: 2em; background: #f7f7f7; } 

        h1 { text-align: center; } 

        table { width: 100%; border-collapse: collapse; margin-top: 1em; background: #fff; } 

        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; } 

        .income { color: green; } 

        .expense { color: red; } 

        .actions a { margin-right: 8px; } 

        .links { margin-top: 2em; text-align: center; } 

        .links a { display: block; margin: 5px 0; text-decoration: none; color: #333; } 

    </style> 

</head> 

<body> 

    <h1><?php echo "{$year}年{$month}月の家計簿"; ?></h1> 

 

    <p style="text-align:center;"> 

        <a href="?year=<?= $year ?>&month=<?= $month - 1 ?>">←前の月</a> | 

        <a href="?year=<?= $year ?>&month=<?= $month + 1 ?>">次の月→</a> 

    </p> 

 

    <table> 

        <thead> 

            <tr> 

                <th>日付</th> 

                <th>カテゴリ</th> 

                <th>金額</th> 

                <th>メモ</th> 

                <th>操作</th> 

            </tr> 

        </thead> 

        <tbody> 

            <?php foreach ($transactions as $t): ?> 

                <tr> 

                    <td><?= htmlspecialchars($t['date']) ?></td> 

                    <td><?= htmlspecialchars($t['category_name']) ?></td> 

                    <td class="<?= $t['type'] === '収入' ? 'income' : 'expense' ?>"> 

                        <?= ($t['type'] === '支出' ? '-' : '+') . number_format($t['amount']) ?>円 

                    </td> 

                    <td><?= htmlspecialchars($t['memo']) ?></td> 

                    <td class="actions"> 

                        <a href="edit.php?id=<?= $t['id'] ?>">編集</a> 

                        <a href="delete.php?id=<?= $t['id'] ?>" onclick="return confirm('本当に削除しますか？');">削除</a> 

                    </td> 

                </tr> 

            <?php endforeach; ?> 

        </tbody> 

    </table> 

 

    <h3>収入合計：<?= number_format($income) ?>円</h3> 

    <h3>支出合計：<?= number_format($expense) ?>円</h3> 

    <h3>差額：<?= number_format($income - $expense) ?>円</h3> 

 

    <div class="links"> 

        <a href="add.php">＋ 新しい記録を追加</a> 

        <a href="category.php">⚙️ カテゴリ管理はこちら</a> 

        <a href="chart.php?year=<?= $year ?>&month=<?= $month ?>">📊 円グラフで見る</a> 

        <a href="graph.php">📈 月別の推移グラフで見る</a> 

        <a href="export_csv.php?year=<?= $year ?>&month=<?= $month ?>">⬇️ 支出だけCSVで出力</a> 

        <a href="export_all_csv.php?year=<?= $year ?>&month=<?= $month ?>">⬇️ 収入・支出まとめてCSV出力</a> 

    </div> 

</body> 

</html> 