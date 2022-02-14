<?php
session_start();
require('library.php');

//check.phpに直接アクセスしたときに会員登録ページにリダイレクトする
if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    header('Location: register.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbconnect();
    $stmt = $db->prepare('insert into members(name, email, password, admin) values(?, ?, ?, ?)');
    if (!$stmt) {
        die($db->error);
    }
    $password = password_hash($form['password'], PASSWORD_DEFAULT);
    $admin = FALSE;
    $stmt->bind_param('sssi', $form['name'], $form['email'], $password, $admin);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    unset($_SESSION['form']);
    header('Location: thanks.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認ページ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="check">
        <div class="check__form">
            <form action="" method="post">
                <input type="hidden" name="action" value="submit">
                <h1 class="check__form-title">入力情報の確認</h1>
                <dl>
                    <div class="check__form-content">
                        <dt>氏名</dt>
                        <dd><?php echo h($form['name']); ?></dd>
                    </div>
                    <div class="check__form-content">
                        <dt>メールアドレス</dt>
                        <dd><?php echo h($form['email']); ?></dd>
                    </div>
                    <div class="check__form-content">
                        <dt>パスワード</dt>
                        <dd>表示されません</dd>
                    </div>
                </dl>
                <a href="register.php?action=rewrite">変更する</a>
                <button type="submit" class="check__form-button">登録する</button>
            </form>
        </div>
    </div>
</body>

</html>