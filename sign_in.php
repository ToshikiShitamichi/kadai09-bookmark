<?php
include("./pdo.php");

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

if (!$record) {
    $err_no = 1;
    header("Location:sign_in.html?status=error{$err_no}");
    exit();
}

if (!password_verify($password, $record["password"])) {
    $err_no = 2;
    header("Location:sign_in.html?status=error{$err_no}");
    exit();
}

header("Location:home.html#{$record["uName"]}");
exit();
