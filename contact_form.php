<?php
session_start();
session_regenerate_id();
require_once('library.php');

if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    $form = [
        'name' => '',
        'email' => '',
        'tel' => '',
        'select' => '',
        'content' => ''
    ];
}

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    if ($form['name'] === '') {
        $error[] = '* 氏名を入力してください';
    } else if (strlen($form['name']) > 16) {
        $error[] = '* 氏名は16文字以下で入力してください';
    }

    $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($form['email'] === '') {
        $error[] = '* メールアドレスを入力してください';
    } else if (strlen($form['email']) > 200) {
        $error[] = '* メールアドレスは200文字以下で入力してください';
    }

    $form['tel'] = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_NUMBER_INT);
    if ($form['tel'] === '') {
        $error[] = '* 電話番号を入力してください。';
    } else if (strlen($form['tel']) > 12) {
        $error[] = '* 電話番号は12文字以下で入力してください';
    }

    $form['select'] = filter_input(INPUT_POST, 'select', FILTER_SANITIZE_STRING);
    if ($form['select'] === '') {
        $error[] = '* 製品種別を選択してください';
    }

    $form['content'] = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    if ($form['content'] === '') {
        $error[] = '* 問い合わせ内容を入力してください';
    } else if (strlen($form['content']) > 2000) {
        $error[] = '* 問い合わせ内容は2000文字以下で入力してください';
    }

    //フォームにエラーがないときにセッションに値を保持し、contact_check.phpに移動
    if (empty($error)) {
        $_SESSION['form'] = $form;
        header('Location: contact_check.php');
        exit();
    }
}

//問い合わせページに直接アクセスしたときにログインページにリダイレクトする
if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
} else {
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせページ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="contact_form">
        <div class="contact_form__inquiry">
            <form action="" method="post">
                <h1 class="contact_form__inquiry-title">お問い合わせフォーム</h1>
                <a href="logout.php" class="contact_form__logout">ログアウト</a>
                <div class="contact_form__inquiry-content">
                    <label for="name" class="contact_form__inquiry-label">氏名</label>
                    <input type="text" id="name" name="name" value="<?php echo h($form['name']); ?>" maxlength="16" class="contact_form__inquiry-input">
                </div>
                <div class="contact_form__inquiry-content">
                    <label for="email" class="contact_form__inquiry-label">メールアドレス</label>
                    <input type="email" id="email" name="email" value="<?php echo h($form['email']); ?>" maxlength="200" class="contact_form__inquiry-input">
                </div>
                <div class="contact_form__inquiry-content">
                    <label for="tel" class="contact_form__inquiry-label">電話番号(ハイフンなし）</label>
                    <input type="tel" id="tel" name="tel" value="<?php echo h($form['tel']); ?>" maxlength="12" class="contact_form__inquiry-input">
                </div>
                <div class="contact_form__inquiry-content">
                    <label for="select" class="contact_form__inquiry-label">製品種別</label>
                    <select name="select" id="select" class="contact_form__inquiry-select">
                        <?php for ($i = 1; $i < 17; $i++) : ?>
                            <?php if ($i < 10) : ?>
                                <option value="<?php echo 'A00' . $i; ?>"><?php echo 'A00' . $i; ?></option>
                            <?php elseif ($i >= 10) : ?>
                                <option value="<?php echo 'A0' . $i; ?>"><?php echo 'A0' . $i; ?></option>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="contact_form__inquiry-content">
                    <label for="content" class="contact_form__inquiry-label">問い合わせ内容</label>
                    <textarea id="content" name="content" value="<?php echo h($form['content']); ?>" cols="60" rows="10" maxlength="2000" class="contact_form__inquiry-textarea"></textarea>
                </div>
                <?php if (isset($error)): ?>
                    <ul class="error-list">
                    <?php foreach($error as $value): ?>
                        <li><?php echo $value; ?></li>
                    <?php endforeach; ?>    
                    </ul>
                <?php endif; ?>
                <div class="contact_form__move-point">
                    <button type="submit" class="contact_form__inquiry-button">確認する</button>
                    <a href="contact_list.php?<?php echo h('未対応'); ?>">問い合わせ一覧ページへ</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>