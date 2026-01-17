<?php
include("./pdo.php");
session_start();
check_session_id();

// ログインユーザー情報を取得
$uId = $_SESSION['user']['uId'];
$uName = $_SESSION['user']['uName'];
$uMail = $_SESSION['user']['uMail'];

// POSTリクエストが来たとき
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    //データ取得
    $uName = $_POST["uName"];
    $uMail = $_POST["uMail"];

    // SQL実行
    $sql = '
SELECT
count(*) as count
FROM
users_table
WHERE
uMail = :uMail
AND
uId != :uId
';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uId', $uId, PDO::PARAM_INT);
    $stmt->bindValue(':uMail', $uMail, PDO::PARAM_STR);
    try {
        $status = $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(["sql error" => "{$e->getMessage()}"]);
        exit();
    }
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // 同一メールアドレスのレコードが存在する場合
    if ($record["count"] === 1) {
        // 入力された情報を保持
        $_SESSION['old'] = [
            'uName' => $uName,
            'uMail' => $uMail,
        ];
        // エラーメッセージを登録
        $_SESSION['errors'] = [
            'uMail' => 'このメールアドレスはすでに使用されています。',
        ];
        // アカウント設定画面再表示
        header("Location:account_update.php");
        exit();
    }

    // SQL実行
    $sql = '
UPDATE
    users_table
SET
    uName = :uName,
    uMail = :uMail,
    updated_at = now()
WHERE
    uId = :uId
';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uId', $uId, PDO::PARAM_INT);
    $stmt->bindValue(':uName', $uName, PDO::PARAM_STR);
    $stmt->bindValue(':uMail', $uMail, PDO::PARAM_STR);
    try {
        $status = $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(["sql error" => "{$e->getMessage()}"]);
        exit();
    }

    // ログインユーザー情報を更新
    $_SESSION['user']['uName'] = $uName;
    $_SESSION['user']['uMail'] = $uMail;

    // サクセスメッセージを登録
    $_SESSION['success'] = 'アカウント情報を更新しました';

    header("Location:account_setting.php");
    exit();
}
$old = $_SESSION['old'] ?? ['uName' => '', 'uMail' => ''];
$errors = $_SESSION['errors'] ?? [];

// 取得したセッションのクリア
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
        <h2 class="account-setting-title">アカウント更新</h2>
        <form class="account-setting-form" action="./account_update.php" method="post">
            <div>
                <input type="text" name="uName" id="uName" placeholder="ユーザー名" required value="<?= $uName ?>">
            </div>
            <div>
                <input type="email" name="uMail" id="uMail" placeholder="メールアドレス" required value="<?= $uMail ?>">
                <!-- エラーメッセージが登録されていれば表示 -->
                <?php if (!empty($errors['uMail'])): ?>
                    <span class="err-msg">
                        <?= $errors['uMail'] ?>
                    </span>
                <?php endif; ?>
            </div>
            <button class="account-setting-btn">更新</button>
        </form>
        <a href="account_setting.php">戻る</a>
    </div>
</body>

</html>