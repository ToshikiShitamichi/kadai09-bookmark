<?php
session_start();
include("./pdo.php");

$uId = $_SESSION['user']['uId'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // SQL実行
    $sql = '
DELETE
FROM
    `users_table`
WHERE
    uId = :uId
';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uId', $uId, PDO::PARAM_INT);
    try {
        $status = $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(["sql error" => "{$e->getMessage()}"]);
        exit();
    }

    header("Location:sign_up.php");
    exit();
}
$old = $_SESSION['old'] ?? ['uName' => '', 'uMail' => ''];
$errors = $_SESSION['errors'] ?? [];

unset($_SESSION['old'], $_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>事業企画</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <div class="account-setting-container">
        <h2 class="account-setting-title">アカウント削除</h2>
        <form class="account-setting-form" action="./account_delete.php" method="post">
            <p style="color: red;">アカウントを削除すると、これまでのデータはすべて削除されます</p>
            <button style="color:white; background-color: red;" class="account-setting-btn">削除</button>
            <a href="./account_setting.php" class="account-setting-btn" style="color:black; text-decoration: none;">キャンセル</a>
        </form>
    </div>
</body>

</html>