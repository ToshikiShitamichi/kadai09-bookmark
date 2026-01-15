<?php
session_start();
include("./pdo.php");

$uId = $_SESSION['user']['uId'];
$uName = $_SESSION['user']['uName'];
$uMail = $_SESSION['user']['uMail'];

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
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
        <h2 class="account-setting-title">アカウント設定</h2>
        <?php if (!empty($success)): ?>
            <p>
                <span class="success-msg"><?= $success ?></span>
            </p>
        <?php endif; ?>
        <a href="home.php">ホーム画面へ</a>
        <form class="account-setting-form" action="./account_update.php" method="post">
            <div>
                <input type="text" name="uName" id="uName" placeholder="ユーザー名" readonly value="<?= $uName ?>">
            </div>
            <div>
                <input type="email" name="uMail" id="uMail" placeholder="メールアドレス" readonly value="<?= $uMail ?>">
            </div>
            <a href="./account_update.php" class="account-setting-btn" style="color:black;background-color:#f0f0f0; text-decoration: none;">編集</a>
        </form>
        <a href="account_delete.php" style="color: red;">アカウントを削除</a>
    </div>
</body>

</html>