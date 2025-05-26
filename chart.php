<?php 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

// 年月取得（GETがなければ今月） 

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y'); 

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); 

$start = "$year-$month-01"; 

$end = date("Y-m-t", strtotime($start)); 

 

// 支出カテゴリごとの合計を取得 

$stmt = $pdo->prepare(" 

    SELECT c.name AS category_name, SUM(t.amount) AS total 

    FROM transactions t 

    JOIN categories c ON t.category_id = c.id 

    WHERE t.date BETWEEN ? AND ? AND c.type = '支出' 

    GROUP BY t.category_id 

"); 

$stmt->execute([$start, $end]); 

$data = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 

// カテゴリ名と合計金額をそれぞれ配列に 

$labels = array_column($data, 'category_name'); 

$totals = array_column($data, 'total'); 

?> 

 

<!DOCTYPE html> 

<html lang="ja"> 

<head> 

    <meta charset="UTF-8"> 

    <title>支出の円グラフ</title> 

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 

    <style> 

        body { font-family: sans-serif; margin: 2em; background: #fff; } 

        canvas { max-width: 600px; margin: auto; display: block; } 

    </style> 

</head> 

<body> 

    <h1><?= "{$year}年{$month}月の支出内訳" ?></h1> 

    <canvas id="pieChart" width="400" height="400"></canvas> 

 

    <p style="text-align:center;"> 

        <a href="?year=<?= $year ?>&month=<?= $month - 1 ?>">←前の月</a> | 

        <a href="?year=<?= $year ?>&month=<?= $month + 1 ?>">次の月→</a> 

    </p> 

    <p style="text-align:center;"><a href="index.php">← 家計簿トップへ戻る</a></p> 

 

    <script> 

        const ctx = document.getElementById('pieChart').getContext('2d'); 

        const pieChart = new Chart(ctx, { 

            type: 'pie', 

            data: { 

                labels: <?= json_encode($labels) ?>, 

                datasets: [{ 

                    data: <?= json_encode($totals) ?>, 

                    backgroundColor: [ 

                        '#f87171', '#facc15', '#34d399', '#60a5fa', 

                        '#a78bfa', '#f472b6', '#fb923c', '#4ade80', 

                        '#fbbf24', '#38bdf8' 

                    ], 

                }] 

            }, 

            options: { 

                responsive: true, 

                plugins: { 

                    legend: { position: 'bottom' }, 

                    title: { display: true, text: 'カテゴリ別支出割合' } 

                } 

            } 

        }); 

    </script> 

</body> 

</html> 