<?php
include("./pdo.php");
session_start();
check_session_id();
check_admin();

// SQL実行
$sql = '
SELECT
    *
FROM
    users_table
WHERE
    is_admin = 0
';

$stmt = $pdo->prepare($sql);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}
$record = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<table border="1">
    <thead>
        <tr>
            <th>ユーザー名</th>
            <th>メールアドレス</th>
            <th>登録日</th>
            <th>削除日</th>
            <th>ユーザー削除</th>
        </tr>
    </thead>
    <tbody>
';
foreach ($record as $row) {
    if ($row['deleted_at']) {
        $is_delete = '<a href="account_delete.php?uId=' . $row['uId'] . '">削除</a>';
    } else {
        $is_delete = "";
    }
    $html .= '
        <tr>
            <td>' . $row["uName"] . '</td>
            <td>' . $row["uMail"] . '</td>
            <td>' . $row["created_at"] . '</td>
            <td>' . $row["deleted_at"] . '</td>
            <td>' . $is_delete . '</td>
        </tr>
    ';
}
$html .= '
    </tbody>
</table>
';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者</title>
</head>

<body>
    管理者メニュー
    <?= $html ?>
    <a href="./home.php">ホーム画面へ</a>
</body>

</html>