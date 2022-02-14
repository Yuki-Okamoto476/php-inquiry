<?php
session_start();
session_regenerate_id();
require('library.php');

if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    $form = [
        'name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => ''
    ];
}
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    if ($form['name'] === '') {
        $error['name'] = 'blank';
    }

    $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($form['email'] === '') {
        $error['email'] = 'blank';
    } else {
        //メールアドレスの重複をチェック
        $db = dbconnect();
        $stmt = $db->prepare('select count(*) from members where email=?');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('s', $form['email']);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
        $stmt->bind_result($cnt);
        $stmt->fetch();
        if ($cnt > 0) {
            $error['email'] = 'duplicate';
        }
    }

    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($form['password'] === '') {
        $error['password'] = 'blank';
    } else if (strlen($form['password']) < 4) {
        $error['password'] = 'length';
    }

    $form['confirm_password'] = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
    if ($form['confirm_password'] === '') {
        $error['confirm_password'] = 'blank';
    } else if ($form['password'] !== $form['confirm_password']) {
        $error['confirm_password'] = 'different';
    }

    //フォームにエラーがないときにセッションに値を保持し、check.phpに移動
    if (empty($error)) {
        $_SESSION['form'] = $form;
        header('Location: check.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="register">
        <div class="register__form">
            <form action="" method="post">
                <h1 class="register__form-title">会員登録</h1>
                <div class="register__form-content">
                    <label for="name" class="register__form-label">氏名</label>
                    <input type="text" id="name" name="name" value="<?php echo h($form['name']); ?>" class="register__form-input">
                    <?php if (isset($error['name']) && $error['name'] === 'blank') : ?>
                        <p class="register__form-error">* 氏名を入力してください</p>
                    <?php endif; ?>
                </div>
                <div class="register__form-content">
                    <label for="email" class="register__form-label">メールアドレス</label>
                    <input type="email" id="email" name="email" value="<?php echo h($form['email']); ?>" class="register__form-input">
                    <?php if (isset($error['email']) && $error['email'] === 'blank') : ?>
                        <p class="register__form-error">* メールアドレスを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['email']) && $error['email'] === 'duplicate') : ?>
                        <p class="register__form-error">* このメールアドレスは既に登録済みです</p>
                    <?php endif; ?>
                </div>
                <div class="register__form-content">
                    <label for="password" class="register__form-label">パスワード</label>
                    <input type="password" id="password" name="password" value="<?php echo h($form['password']); ?>" class="register__form-input">
                    <?php if (isset($error['password']) && $error['password'] === 'blank') : ?>
                        <p class="register__form-error">* パスワードを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['password']) && $error['password'] === 'length') : ?>
                        <p class="register__form-error">* パスワードは４文字以上で入力してください</p>
                    <?php endif; ?>
                </div>
                <div class="register__form-content">
                    <label for="confirm_password" class="register__form-label">確認用パスワード</label>
                    <input type="password" id="confirm_password" name="confirm_password" value="<?php echo h($form['confirm_password']); ?>" class="register__form-input">
                    <?php if (isset($error['confirm_password']) && $error['confirm_password'] === 'blank') : ?>
                        <p class="register__form-error">* パスワードをもう一度入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['confirm_password']) && $error['confirm_password'] === 'different') : ?>
                        <p class="register__form-error">* パスワードが一致しません</p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="register__form-button">確認する</button>
            </form>
        </div>
    </div>
</body>

</html>