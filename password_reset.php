<?php
require('library.php');

$random_id = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_STRING);
if (!$random_id) {
    header('Location: password_reser_mail.php');
    exit();
}

$error = [];
$password = '';
$changed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($password === '') {
        $error['reset'] = 'blank';
    } else {
        $db = dbconnect();
        $stmt = $db->prepare('update members set password=? where password_url=?');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('ss', $hash, $random_id);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
        $changed = true;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワードリセット</title>
</head>

<body>
    <p>パスワードを変更してください。</p>
    <form action="" method="post">
        <div>
            <label for="password">新しいパスワード</label>
            <input type="password" name="password" id="password">
        </div>
        <?php if (isset($error['reset']) && $error['reset'] === 'blank') : ?>
            <p class="login__form-error">* パスワードを入力してください</p>
        <?php endif; ?>
        <button type="submit">変更する</button>
        <?php if ($changed) : ?>
            <p>パスワードを変更しました</p>
        <?php endif; ?>
    </form>
    <a href="index.php">ログイン画面に戻る</a>
</body>

</html>