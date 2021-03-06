<?php
require_once('library.php');

$error = [];
$email = '';
$url = '';
$title = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($email === '') {
        $error[] = '* メールアドレスを入力してください';
    } else {
        $db = dbconnect();
        $stmt = $db->prepare('select * from members where email=?');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('s', $email);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
        $data = $stmt->fetch();
        if (!$data) {
            $error[] = '* 存在しないメールアドレスです';
        } else {
            $random_id = uniqid();
            $url = 'http://localhost:8888/php-inquiry/password_reset.php?url=' . $random_id;
            mb_language('Japanese');
            mb_internal_encoding('UTF-8');
            $title = 'パスワードリセットについて';

            $db = dbconnect();
            $stmt = $db->prepare('update members set password_url=? where email=?');
            if (!$stmt) {
                die($db->error);
            }
            $stmt->bind_param('ss', $random_id, $email);
            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード変更メール送信</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <p>メールアドレスを入力してください。</p>
    <form action="" method="post">
        <div>
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email">
        </div>
        <?php if (isset($error)) : ?>
            <ul class="error-list">
                <?php foreach ($error as $value) : ?>
                    <li><?php echo $value; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <p>※リセットボタンを押すとパスワードリセットのメールが送信されます。</p>
        <button type="submit">リセット</button>
    </form>
    <?php if (mb_send_mail($email, $title, $url)) : ?>
        <p>メールを送信しました。</p>
    <?php endif; ?>
    <a href="index.php">ログイン画面に戻る</a>
</body>

</html>