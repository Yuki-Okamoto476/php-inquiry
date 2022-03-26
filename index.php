<?php
session_start();
require_once('library.php');

$error = [];
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($email === '' || $password === '') {
        $error[] = '* メールアドレスとパスワードを入力してください';
    } else {
        //ログインチェック
        $db = dbconnect();
        $stmt = $db->prepare('select id, name, password, admin from members where email=?');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('s', $email);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
        $stmt->bind_result($id, $name, $hash, $admin);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            session_regenerate_id();
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['admin'] = $admin;
            header('Location: contact_form.php');
            exit();
        } else {
            $error[] = '* メールアドレスもしくはパスワードが間違っています';
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
    <title>ログインページ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login">
        <div class="login__form">
            <form action="" method="post">
                <h1 class="login__form-title">ログイン</h1>
                <div class="login__form-content">
                    <label for="email" class="login__form-label">メールアドレス</label>
                    <input type="email" id="email" name="email" value="<?php echo h($email); ?>" class="login__form-input">
                </div>
                <div class="login__form-content">
                    <label for="password" class="login__form-label">パスワード</label>
                    <input type="password" id="password" name="password" value="<?php echo h($password); ?>" class="login__form-input">
                </div>
                <?php if (isset($error)): ?>
                    <ul class="error-list">
                    <?php foreach($error as $value): ?>
                        <li><?php echo $value; ?></li>
                    <?php endforeach; ?>    
                    </ul>
                <?php endif; ?>
                <div class="login__form-move-point">
                    <button type="submit" class="login__form-button">ログイン</button>
                    <a href="register.php">新規登録はこちら</a>
                    <a href="password_reset_mail.php">パスワードを忘れた方はこちら</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>