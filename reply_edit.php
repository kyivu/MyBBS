<?php
//データベース接続を行うファイル(dbConnect.php)を実行する
require('dbConnect.php');

session_start();

//reply_detail.phpで編集ボタン押されたら、編集するidをセッションに登録
if (isset($_POST['rep_edit_id'])) {
    $_SESSION['rep_id']  = $_POST['rep_edit_id'];
}

//編集ボタンを押されたら、　
if (isset($_POST['edit'])) {

    //編集する各情報を取得
    $new_reply_name = $_POST['new_reply_name'];
    $new_reply_text = $_POST['new_reply_text'];

    try {
        $sql = "UPDATE replies SET reply_name = :new_reply_name, reply_text = :new_reply_text WHERE reply_id = :edit_id ";  //登録するSQL文を代入
        $stmt = $dbh->prepare($sql); //$sqlで定義されたSQL文がデータベースに送信され、実行の準備が整う
        $stmt->bindValue(':new_reply_name', $new_reply_name, PDO::PARAM_STR);
        $stmt->bindValue(':new_reply_text', $new_reply_text, PDO::PARAM_STR);
        $stmt->bindValue(':edit_id', $_SESSION['rep_id'], PDO::PARAM_STR);
        $stmt->execute(); //sql文が実行される

        echo "編集実行";
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    //掲示板TOPへ戻る
    header('Location:/MyBBS/reply_detail.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>返信編集画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <h2>返信編集画面</h2>
    <form action="" method="POST">

        <!-- 選択したidを取得するファイル(getReply.php)を実行する -->
        <?php require('getReply.php'); ?>

        <!-- 検索した編集する返信の情報を表示する -->
        投稿者<input type="text" name="new_reply_name" value="<?php echo $result['reply_name'] ?>" class="form-control"><br>
        本文<br>
        <textarea rows="3" cols="50" name="new_reply_text" class="form-control"><?php echo $result['reply_text'] ?></textarea><br>

        <div class="button1">
            <button type="submit" name="edit">編集</button>
            <button type="submit" name="cancel" formaction="reply_detail.php">キャンセル</button>
        </div>

    </form>
    <br>

</body>

</html>

<?php
//データーベースの接続を切る
unset($dbh);
?>