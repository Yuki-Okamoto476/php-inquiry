<?php
session_start();
require('library.php');

if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    header('Location: contact_form.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbconnect();
    $stmt = $db->prepare('insert into inquiries(name, email, tel, selection, content, admin, status, responder, message) values(?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        die($db->error);
    }
    $admin = FALSE;
    $status = '未対応';
    $responder = '';
    $message = '';
    $stmt->bind_param('ssississs', $form['name'], $form['email'], $form['tel'], $form['select'], $form['content'], $admin, $status, $responder, $message);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    unset($_SESSION['form']);
    header('Location: contact_thanks.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ内容確認</title>
</head>

<body>
    <div class="contact_check">
        <div class="contact_check__form">
            <form action="" method="post">
                <input type="hidden" name="action" value="submit">
                <h1 class="contact_check__form-title">入力情報の確認</h1>
                <dl>
                    <div class="contact_check__form-content">
                        <dt>氏名</dt>
                        <dd><?php echo h($form['name']); ?></dd>
                    </div>
                    <div class="contact_check__form-content">
                        <dt>メールアドレス</dt>
                        <dd><?php echo h($form['email']); ?></dd>
                    </div>
                    <div class="contact_check__form-content">
                        <dt>電話番号</dt>
                        <dd><?php echo h($form['tel']); ?></dd>
                    </div>
                    <div class="contact_check__form-content">
                        <dt>製品種別</dt>
                        <dd><?php echo h($form['select']); ?></dd>
                    </div>
                    <div class="contact_check__form-content">
                        <dt>問い合わせ内容</dt>
                        <dd><?php echo h($form['content']); ?></dd>
                    </div>
                </dl>
                <a href="contact_form.php?action=rewrite">変更する</a>
                <button type="submit" class="contact_check__form-button">提出する</button>
            </form>
        </div>
    </div>
</body>

</html>