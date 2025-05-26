<?php 

$pdo = new PDO('mysql:host=localhost;dbname=kakeibo_db;charset=utf8mb4', 'root', ''); 

 

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y'); 

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); 

$start = "$year-$month-01"; 

$end = date("Y-m-t", strtotime($start)); 

 

// 支出のみ取得 

$stmt = $pdo->prepare(" 

    SELECT t.date, c.name AS category_name, t.amount, t.memo 

    FROM transactions t 

    JOIN categories c ON t.category_id = c.id 

    WHERE t.date BETWEEN ? AND ? AND c.type = '支出' 

    ORDER BY t.date ASC 

"); 

$stmt->execute([$start, $end]); 

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 

// ファイル名 

$filename = sprintf('%04d_%02d_shishutsu.csv', $year, $month); 

 

// ヘッダー出力（Shift_JISにする！） 

header('Content-Type: text/csv; charset=Shift_JIS'); 

header("Content-Disposition: attachment; filename=$filename"); 

 

// 出力ストリームを開く 

$fp = fopen('php://output', 'w'); 

 

// ヘッダー行（Shift_JISに変換して出力） 

$header = ['日付', 'カテゴリ', '金額', 'メモ']; 

fputcsv($fp, array_map(function($v) { return mb_convert_encoding($v, 'SJIS-win', 'UTF-8'); }, $header)); 

 

// データ行（同様に変換） 

foreach ($rows as $row) { 

    $line = [ 

        $row['date'], 

        $row['category_name'], 

        $row['amount'], 

        $row['memo'] 

    ]; 

    fputcsv($fp, array_map(function($v) { return mb_convert_encoding($v, 'SJIS-win', 'UTF-8'); }, $line)); 

} 

 

fclose($fp); 

exit; 