<?php
session_start();
include("./pdo.php");

// POSTリクエストが来たとき
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //データ取得
    $uMail = $_POST["uMail"];
    $password = $_POST["password"];

    // SQL実行
    $sql = '
SELECT
    uName,
    uMail,
    password
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

    // 同一メールアドレスのレコードが存在しない場合
    if (!$record) {
        // 入力された情報を保持
        $_SESSION['old'] = ['uMail' => $uMail];
        // エラーメッセージを登録
        $_SESSION['errors'] = ['uMail' => 'メールアドレスが正しくありません'];
        // サインイン画面再表示
        header("Location:sign_in.php");
        exit();
    }

    // パスワードが一致しない
    if (!password_verify($password, $record["password"])) {
        // 入力された情報を保持
        $_SESSION['old'] = ['uMail' => $uMail];
        // エラーメッセージを登録
        $_SESSION['errors'] = ['password' => 'パスワードが正しくありません'];
        // サインイン画面再表示
        header("Location:sign_in.php");
        exit();
    }

    // ログインユーザー情報を保持
    $_SESSION['user'] = [
        'uName' => $record['uName'],
        'uMail' => $record['uMail'],
    ];

    // ホーム画面に遷移
    header("Location:home.php");
    exit();
}
$old = $_SESSION['old'] ?? ['uMail' => ''];
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
    <div class="signin-container">
        <h2 class="signin-title">ログイン</h2>
        <form class="signin-form" action="./sign_in.php" method="post">
            <div>
                <input type="email" name="uMail" id="uMail" placeholder="メールアドレス" required value="<?= $old["uMail"] ?>">
                <!-- エラーメッセージが登録されていれば表示 -->
                <?php if (!empty($errors['uMail'])): ?>
                    <span class="err-msg">
                        <?= $errors['uMail'] ?>
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <input type="password" name="password" id="password" placeholder="パスワード" required>
                <!-- エラーメッセージが登録されていれば表示 -->
                <?php if (!empty($errors['password'])): ?>
                    <span class="err-msg">
                        <?= $errors['password'] ?>
                    </span>
                <?php endif; ?>
            </div>
            <button class="signin-btn">ログイン</button>
        </form>
        <hr>
        <div class="signin-btn-group">
            <a class="signin-btn">Googleで続ける</a>
            <a class="signin-btn">GitHubで続ける</a>
        </div>
        <span>アカウントをお持ちでないですか？<a href="./sign_up.php">アカウントを作成</a></span>
    </div>
</body>

</html>