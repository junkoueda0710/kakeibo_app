<?php 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

// 直近12ヶ月分の収支を取得 

$data = []; 

 

for ($i = 11; $i >= 0; $i--) { 

    $month = date('Y-m-01', strtotime("-$i months")); 

    $yearMonth = date('Y年n月', strtotime($month)); 

    $start = $month; 

    $end = date("Y-m-t", strtotime($start)); 

 

    $stmt = $pdo->prepare(" 

        SELECT c.type, SUM(t.amount) as total 

        FROM transactions t 

        JOIN categories c ON t.category_id = c.id 

        WHERE t.date BETWEEN ? AND ? 

        GROUP BY c.type 

    "); 

    $stmt->execute([$start, $end]); 

    $totals = ['収入' => 0, '支出' => 0]; 

 

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) { 

        $totals[$row['type']] = $row['total']; 

    } 

 

    $data[] = [ 

        'month' => $yearMonth, 

        'income' => $totals['収入'], 

        'expense' => $totals['支出'] 

    ]; 

} 

 

// ラベルとデータ配列へ 

$labels = array_column($data, 'month'); 

$incomes = array_column($data, 'income'); 

$expenses = array_column($data, 'expense'); 

?> 

 

<!DOCTYPE html> 

<html lang="ja"> 

<head> 

    <meta charset="UTF-8"> 

    <title>月別の収支推移</title> 

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 

    <style> 

        body { font-family: sans-serif; margin: 2em; background: #fff; } 

        canvas { max-width: 800px; margin: auto; display: block; } 

    </style> 

</head> 

<body> 

    <h1 style="text-align:center;">月別の収支推移</h1> 

    <canvas id="barChart" width="800" height="400"></canvas> 

    <p style="text-align:center;"><a href="index.php">← 家計簿トップへ戻る</a></p> 

 

    <script> 

        const ctx = document.getElementById('barChart').getContext('2d'); 

        const barChart = new Chart(ctx, { 

            type: 'bar', 

            data: { 

                labels: <?= json_encode($labels) ?>, 

                datasets: [ 

                    { 

                        label: '収入', 

                        data: <?= json_encode($incomes) ?>, 

                        backgroundColor: '#4ade80' 

                    }, 

                    { 

                        label: '支出', 

                        data: <?= json_encode($expenses) ?>, 

                        backgroundColor: '#f87171' 

                    } 

                ] 

            }, 

            options: { 

                responsive: true, 

                plugins: { 

                    legend: { position: 'top' }, 

                    title: { display: true, text: '直近12ヶ月の収支推移' } 

                }, 

                scales: { 

                    y: { 

                        beginAtZero: true, 

                        ticks: { 

                            callback: function(value) { 

                                return value.toLocaleString() + '円'; 

                            } 

                        } 

                    } 

                } 

            } 

        }); 

    </script> 

</body> 

</html> 