<?php
session_start();
require_once('library.php');

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

$db = dbconnect();
$stmt = $db->prepare('select id, name, email, tel, selection, content, created_at, status, responder, responded_at, message from inquiries where status=? order by created_at asc');
if (!$stmt) {
    die($db->error);
}
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$unsupported = '未対応';
$supporting = '対応中';
$supported = '対応済み';
if ($status === '未対応') {
    $stmt->bind_param('s', $unsupported);
    $unsupported_style = 'unsupported_button';
    $supporting_style = '';
    $supported_style = '';
} else if ($status === '対応中') {
    $stmt->bind_param('s', $supporting);
    $unsupported_style = '';
    $supporting_style = 'supporting_button';
    $supported_style = '';
} else if ($status === '対応済み') {
    $stmt->bind_param('s', $supported);
    $unsupported_style = '';
    $supporting_style = '';
    $supported_style = 'supported_button';
} else {
    $stmt->bind_param('s', $unsupported);
    $unsupported_style = 'unsupported_button';
    $supporting_style = '';
    $supported_style = '';
}
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}
$stmt->bind_result($id, $name, $email, $tel, $selection, $content, $created_at, $status, $responder, $responded_at, $message);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ一覧</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="contact_list">
        <h1 class="contact_list__title">お問い合わせ一覧</h1>
        <a href="logout.php" class="contact_list__logout">ログアウト</a>
        <form action="" method="get" class="contact_list__filter-buttons">
            <button type="submit" name="status" value="未対応" class="contact_list__filter-button <?php echo h($unsupported_style); ?>">未対応</button>
            <button type="submit" name="status" value="対応中" class="contact_list__filter-button <?php echo h($supporting_style); ?>">対応中</button>
            <button type="submit" name="status" value="対応済み" class="contact_list__filter-button <?php echo h($supported_style); ?>">対応済み</button>
        </form>
        <div class="contact_list__cards">
            <?php while ($stmt->fetch()) : ?>
                <dl class="contact_list__card">
                    <?php if ($_SESSION['admin']) : ?>
                        <div class="contact_list__content">
                            <dt class="contact_list__label">氏名</dt>
                            <dd><?php echo h($name); ?></dd>
                        </div>
                        <div class="contact_list__content">
                            <dt class="contact_list__label">メールアドレス</dt>
                            <dd><?php echo h($email); ?></dd>
                        </div>
                        <div class="contact_list__content">
                            <dt class="contact_list__label">電話番号</dt>
                            <dd><?php echo h('0' . $tel); ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="contact_list__content">
                        <dt class="contact_list__label">製品種別</dt>
                        <dd><?php echo h($selection); ?></dd>
                    </div>
                    <div class="contact_list__content">
                        <dt class="contact_list__label">問い合わせ内容</dt>
                        <?php if (strlen($content) > 100) : ?>
                            <?php $str_content = mb_substr($content, 0, 99) . '...'; ?>
                            <dd><?php echo h($str_content); ?></dd>
                        <?php else : ?>
                            <dd><?php echo h($content); ?></dd>
                        <?php endif; ?>
                    </div>
                    <div class="contact_list__content">
                        <dt class="contact_list__label">問い合わせ日時</dt>
                        <dd><?php echo h($created_at); ?></dd>
                    </div>
                    <div class="contact_list__content">
                        <dt class="contact_list__label">対応状況</dt>
                        <dd class="contact_list__status"><?php echo h($status); ?></dd>
                    </div>
                    <button class="contact_list__button">詳細を見る</button>
                    <div class="contact_list__detail-info">
                        <?php if (strlen($content) > 100) : ?>
                            <div class="contact_list__content">
                                <dt class="contact_list__label">問い合わせ内容（全文）</dt>
                                <dd><?php echo h($content); ?></dd>
                            </div>
                        <?php endif; ?>
                        <div class="contact_list__content">
                            <dt class="contact_list__label">対応者</dt>
                            <?php if ($responder === '') : ?>
                                <dd>対応者はいません</dd>
                            <?php else : ?>
                                <dd><?php echo h($responder); ?></dd>
                            <?php endif; ?>
                        </div>
                        <div class="contact_list__content">
                            <dt class="contact_list__label">対応日時</dt>
                            <?php if ($responded_at === $created_at) : ?>
                                <dd>対応日時は未定です</dd>
                            <?php else : ?>
                                <dd><?php echo h($responded_at); ?></dd>
                            <?php endif; ?>
                        </div>
                        <div class="contact_list__content">
                            <dt class="contact_list__label">回答</dt>
                            <?php if ($message === '') : ?>
                                <dd>回答はありません</dd>
                            <?php else : ?>
                                <dd><?php echo h($message); ?></dd>
                            <?php endif; ?>
                        </div>
                        <?php if ($_SESSION['admin'] && ($responder === '' || $responder === $_SESSION['name'])) : ?>
                            <form action="" method="post" name="form">
                                <a href="contact_chat.php?id=<?php echo h($id); ?>">対応を開始する</a>
                            </form>
                        <?php endif; ?>
                    </div>
                </dl>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="contact_list.js"></script>
</body>

</html>