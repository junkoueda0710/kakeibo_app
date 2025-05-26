<?php 

 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y'); 

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); 

$start = "$year-$month-01"; 

$end = date("Y-m-t", strtotime($start)); 

 

// 収支すべて取得 

$stmt = $pdo->prepare(" 

    SELECT t.date, c.type, c.name AS category_name, t.amount, t.memo 

    FROM transactions t 

    JOIN categories c ON t.category_id = c.id 

    WHERE t.date BETWEEN ? AND ? 

    ORDER BY t.date ASC 

"); 

$stmt->execute([$start, $end]); 

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 

// ファイル名 

$filename = sprintf('%04d_%02d_kakeibo.csv', $year, $month); 

 

// ヘッダー出力（Shift_JIS対応） 

header('Content-Type: text/csv; charset=Shift_JIS'); 

header("Content-Disposition: attachment; filename=$filename"); 

 

$fp = fopen('php://output', 'w'); 

 

// ヘッダー行 

$header = ['日付', 'タイプ', 'カテゴリ', '金額', 'メモ']; 

fputcsv($fp, array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'), $header)); 

 

// データ行 

foreach ($rows as $row) { 

    $line = [ 

        $row['date'], 

        $row['type'], 

        $row['category_name'], 

        $row['amount'], 

        $row['memo'] 

    ]; 

    fputcsv($fp, array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'), $line)); 

} 

 

fclose($fp); 

exit; 