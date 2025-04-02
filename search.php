<?php
header("Content-Type: application/json");

// エラーログ出力を有効化（Azure Web App用）
ini_set("log_errors", 1);
ini_set("error_log", "/home/LogFiles/error_log");
error_log("🔥 DB接続チェック開始");

// 1. パラメータチェック（社員番号）
if (!isset($_GET['employee_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "社員番号が指定されていません"
    ]);
    exit;
}
$employeeId = $_GET['employee_id'];

// 2. Azure SQL Database 接続情報（PDO_SQLSRV）
$server   = "tcp:sqlsrv-foremployeedb.database.windows.net,1433";
$database = "employeedb";
$user     = "sqladmin";
$password = "Test1997726!";

// PDO_SQLSRV DSN構成
$dsn = "sqlsrv:Server=$server;Database=$database";

try {
    // 接続開始
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("✅ DB接続成功");

    // クエリ実行
    $stmt = $pdo->prepare("SELECT name FROM employee_data WHERE employee_id = ?");
    $stmt->execute([$employeeId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(["success" => true, "name" => $result['name']]);
    } else {
        echo json_encode(["success" => false, "message" => "該当する社員が見つかりませんでした。"]);
    }

} catch (PDOException $e) {
    // エラーログ出力
    error_log("❌ DB接続エラー: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "SQL Serverへの接続に失敗しました"
    ]);
}
