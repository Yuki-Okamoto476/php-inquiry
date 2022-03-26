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
        $error[] = '* 氏名を入力してください';
    }

    $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($form['email'] === '') {
        $error[] = '* メールアドレスを入力してください';
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
            $error[] = '* このメールアドレスは既に登録済みです';
        }
    }

    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($form['password'] === '') {
        $error[] = '* パスワードを入力してください';
    } else if (strlen($form['password']) < 4) {
        $error[] = '* パスワードは４文字以上で入力してください';
    }

    $form['confirm_password'] = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
    if ($form['confirm_password'] === '') {
        $error[] = '* パスワードをもう一度入力してください';
    } else if ($form['password'] !== $form['confirm_password']) {
        $error[] = '* パスワードが一致しません';
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
                </div>
                <div class="register__form-content">
                    <label for="email" class="register__form-label">メールアドレス</label>
                    <input type="email" id="email" name="email" value="<?php echo h($form['email']); ?>" class="register__form-input">
                </div>
                <div class="register__form-content">
                    <label for="password" class="register__form-label">パスワード</label>
                    <input type="password" id="password" name="password" value="<?php echo h($form['password']); ?>" class="register__form-input">
                </div>
                <div class="register__form-content">
                    <label for="confirm_password" class="register__form-label">確認用パスワード</label>
                    <input type="password" id="confirm_password" name="confirm_password" value="<?php echo h($form['confirm_password']); ?>" class="register__form-input">
                </div>
                <?php if (isset($error)): ?>
                    <ul class="error-list">
                    <?php foreach($error as $value): ?>
                        <li><?php echo $value; ?></li>
                    <?php endforeach; ?>    
                    </ul>
                <?php endif; ?>
                <button type="submit" class="register__form-button">確認する</button>
            </form>
        </div>
    </div>
</body>

</html>