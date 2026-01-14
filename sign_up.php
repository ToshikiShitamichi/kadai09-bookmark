<?php
include("./pdo.php");

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

if ($record["count"] === 1) {
    $err_no = 1;
    header("Location:sign_up.html?status=error{$err_no}");
    exit();
}

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


header("Location:home.html");
exit();
