<?php
session_start();
include("./pdo.php");

if (isset($_SESSION["session_id"]) && $_SESSION["session_id"] === session_id()){
    session_regenerate_id(true);
    $_SESSION["session_id"] = session_id();
    header('Location:home.php');
    exit();
}

// POSTリクエストが来たとき
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    //データ取得
    $uName = $_POST["uName"];
    $uMail = $_POST["uMail"];
    $raw_password = $_POST["password"];

    // SQL実行
    $sql = '
SELECT
count(*) as count
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

    // 同一メールアドレスのレコード件数
    if ($record["count"] === 1) {
        // 入力された情報を保持
        $_SESSION['old'] = [
            'uName' => $uName,
            'uMail' => $uMail,
        ];
        // エラーメッセージを登録
        $_SESSION['errors'] = [
            'uMail' => 'このメールアドレスはすでに登録されています。',
        ];
        // サインアップ画面再表示
        header("Location:sign_up.php");
        exit();
    }

    // パスワードのハッシュ化
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    // SQL実行
    $sql = '
INSERT INTO users_table(
    uId,
    uName,
    uMail,
    password,
    is_admin,
    created_at,
    updated_at,
    deleted_at
)
VALUES(
    NULL,
    :uName,
    :uMail,
    :password,
    0,
    now(),
    now(),
    NULL
)
';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uName', $uName, PDO::PARAM_STR);
    $stmt->bindValue(':uMail', $uMail, PDO::PARAM_STR);
    $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
    try {
        $status = $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(["sql error" => "{$e->getMessage()}"]);
        exit();
    }

    // SQL実行
    $sql = '
SELECT
    *
FROM
    users_table
WHERE
    uMail = :uMail
AND
    deleted_at is NULL
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

    // ログインユーザー情報を保持
    $_SESSION['session_id'] = session_id();
    $_SESSION['user'] = [
        'uId' => $record['uId'],
        'uName' => $record['uName'],
        'uMail' => $record['uMail'],
        'is_admin' => $record['is_admin']
    ];

    // ホーム画面に遷移
    header("Location:home.php");
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
    <div class="signup-container">
        <h2 class="signup-title">アカウントを作成</h2>
        <form class="signup-form" action="./sign_up.php" method="post">
            <div>
                <input type="text" name="uName" id="uName" placeholder="ユーザー名" required value="<?= $old["uName"] ?>">
            </div>
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
            </div>
            <button class="signup-btn">新規登録</button>
        </form>
        <hr>
        <div class="signup-btn-group">
            <a class="signup-btn">Googleで続ける</a>
            <a class="signup-btn">GitHubで続ける</a>
        </div>
        <span>すでにアカウントをお持ちですか？<a href="./sign_in.php">ログインする</a></span>
    </div>
</body>

</html>