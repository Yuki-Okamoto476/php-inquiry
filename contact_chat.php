<?php
session_start();
session_regenerate_id();
require_once('library.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: contact_list.php');
    exit();
}

$db = dbconnect();
$stmt = $db->prepare('select content, status from inquiries where id=?');
if (!$stmt) {
    die($db->error);
}
$stmt->bind_param('i', $id);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}
$stmt->bind_result($content, $status);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supporting_update = filter_input(INPUT_POST, 'supporting_update', FILTER_SANITIZE_STRING);
    $confirm = filter_input(INPUT_POST, 'confirm', FILTER_SANITIZE_STRING);
    if (isset($supporting_update)) {
        $db = dbconnect();
        $stmt = $db->prepare('update inquiries set status=? where id=?');
        if (!$stmt) {
            die($db->error);
        }
        $supporting = '対応中';
        $stmt->bind_param('si', $supporting, $id);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
    } elseif (isset($confirm)) {
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        $_SESSION['message'] = $message;
        $_SESSION['id'] = $id;
        header('Location: chat_check.php?id=' . $id);
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
    <title>チャット</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="contact_chat">
        <?php if ($stmt->fetch()) : ?>
            <p><?php echo '問い合わせ内容：' . h($content) ?></p>
        <?php else : ?>
            <p>お問い合わせが削除されたか、URLが間違っています</p>
        <?php endif; ?>
        <form action="" method="post">
            <button type="submit" name="supporting_update" class="contact_chat__form-button">対応中にする</button>
        </form>
        <form action="" method="post">
            <textarea name="message" id="message" cols="30" rows="10" placeholder="回答を入力してください" class="contact_chat__form-textarea"></textarea>
            <button type="submit" name="confirm" class="contact_chat__form-button">確認する</button>
        </form>
    </div>
</body>

</html>