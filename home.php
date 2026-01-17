<?php
include("./pdo.php");
session_start();
check_session_id();

// ログインユーザー情報を取得
$uMail = $_SESSION['user']['uMail'];

// SQL実行
$sql = '
SELECT
    uId,
    uName,
    uMail
FROM
    users_table
WHERE
    uMail = :uMail
';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':uMail', $uMail, PDO::PARAM_STR);
try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}
$record = $stmt->fetch(PDO::FETCH_ASSOC);

// ユーザーIDをセッションに保持
$_SESSION['user']['uId'] = $record['uId'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>事業企画</title>
</head>

<body>
    <h1>ダッシュボード</h1>
    <img class="account-icon" src="./default.png" alt="" style="width: 50px;">
    <ul class="dropdown" style="display: none;">
        <li style="list-style: none;">
            <a href="./account_setting.php">アカウント設定</a>
        </li>
    </ul>
    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(".account-icon").on("click", function() {
            $(".dropdown").css("display", "block");
        });
    </script>
</body>

</html>