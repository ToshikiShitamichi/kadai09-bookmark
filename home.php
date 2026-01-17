<?php
include("./pdo.php");
session_start();
check_session_id();

$is_admin = $_SESSION["user"]["is_admin"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>事業企画</title>
</head>

<body>
    <?php if ($is_admin === 1): ?>
        <a href="./admin.php">管理者画面</a>
    <?php endif ?>
    <h1>ダッシュボード</h1>
    <img class="account-icon" src="./default.png" alt="" style="width: 50px;">
    <ul class="dropdown" style="display: none;">
        <li style="list-style: none;">
            <a href="./account_setting.php">アカウント設定</a>
            <a href="./sign_out.php">サインアウト</a>
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