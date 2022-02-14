<?php
session_start();
require('library.php');

$message = $_SESSION['message'];
$id = $_SESSION['id'];

$db = dbconnect();
$stmt = $db->prepare('select email from inquiries where id=?');
if (!$stmt) {
    die($db->error);
}
$stmt->bind_param('i', $id);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}
$stmt->bind_result($email);
$stmt->fetch();

mb_language('Japanese');
mb_internal_encoding('UTF-8');
$title = 'お問い合わせに対する回答';
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>回答完了ページ</title>
</head>

<body>
    <?php if (mb_send_mail($email, $title, $message)): ?>
      <p>回答を送信しました</p>
    <?php else: ?>  
      <p>回答の送信に失敗しました</p>
    <?php endif; ?>  
    <a href="contact_list.php">お問い合わせ一覧ページへ</a>
</body>

</html>