<?php 
session_start();
require('library.php');

//chat_check.phpに直接アクセスしたときにお問い合わせ一覧ページにリダイレクトする
if (isset($_SESSION['message']) && isset($_SESSION['id'])) {
    $message = $_SESSION['message'];
    $id = $_SESSION['id'];
} else {
    header('Location: contact_list.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbconnect();
    $stmt = $db->prepare('update inquiries set status=?, responder=?, message=? where id=?');
    if (!$stmt) {
        die($db->error);
    }
    $supported = '対応済み';
    $responder = $_SESSION['name'];
    $stmt->bind_param('sssi',$supported, $responder, $message, $id );
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    header('Location: chat_thanks.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>回答確認ページ</title>
</head>
<body>
    <div class="chat_check">
        <div class="chat_check__form">
            <form action="" method="post">
                <h1>入力情報の確認</h1>
                <dl>
                    <div class="chat_check__content">
                        <dt>回答</dt>
                        <dd><?php echo h($message); ?></dd>
                    </div>
                </dl>
                <button type="submit">送信する</button>
            </form>
        </div>
    </div>
</body>
</html>