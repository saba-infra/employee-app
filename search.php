<?php
header("Content-Type: application/json");

// 1. 社員番号の取得チェック
if (!isset($_GET['employee_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "社員番号が指定されていません"
    ]);
    exit;
}
$employeeId = $_GET['employee_id'];

// 2. SQL Server接続設定
$serverName = "DB-VM1"; // あなたのSQLサーバー名に合わせてね
$connectionOptions = [
    "Database" => "EmployeeDB",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8" // ← これを追加！
];


// 3. 接続
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    // エラー詳細を取得
    $errors = sqlsrv_errors();
    $errorMessages = [];
    foreach ($errors as $error) {
        $errorMessages[] = "SQLSTATE: {$error['SQLSTATE']}, Code: {$error['code']}, Message: {$error['message']}";
    }

    // ログに出力
    error_log("【SQL接続エラー】" . implode(" | ", $errorMessages));

    echo json_encode([
        "success" => false,
        "message" => "SQL Serverへの接続に失敗しました"
    ]);
    exit;
}

// 4. クエリ実行
$sql = "SELECT name FROM employee_data WHERE employee_id = ?";
$params = [$employeeId];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode([
        "success" => false,
        "message" => "SQLクエリの実行に失敗しました"
    ]);
    exit;
}

// 5. 結果取得と返却
$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if ($result) {
    echo json_encode([
        "success" => true,
        "name" => $result['name']
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "該当する社員が見つかりませんでした。"
    ]);
}

sqlsrv_close($conn);
?>
