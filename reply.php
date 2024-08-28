<?php
//データベース接続を行うファイル(dbConnect.php)を実行する
require('dbConnect.php');

session_start();

//index.phpで返信ボタン押されたら、返信する投稿のidをセッションに登録する
if (isset($_POST['reply_id'])) {
    $_SESSION['id'] = $_POST['reply_id'];
}

//返信ボタンを押されたら、
if (isset($_POST['reply'])) {

    //データベースに登録する返信に関する各情報を取得
    $reply_name = $_POST['reply_name'];
    $reply_text = $_POST['reply_text'];
    $reply_datetime = date('Y-m-d H:i:s');

    try {
        $sql = "INSERT INTO replies(reply_name,reply_datetime,reply_text,post_id) VALUES (:reply_name,:reply_datetime,:reply_text,:post_id)";  //登録するSQL文を代入
        $stmt = $dbh->prepare($sql); //$sqlで定義されたSQL文がデータベースに送信され、実行の準備が整う
        $stmt->bindValue(':reply_name', $reply_name, PDO::PARAM_STR);
        $stmt->bindValue(':reply_datetime', $reply_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':reply_text', $reply_text, PDO::PARAM_STR);
        $stmt->bindValue(':post_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->execute(); //sql文が実行される

    } catch (Exception $e) {
        echo $e->getMessage();
    }

    //掲示板TOPへ戻る
    header('Location:/MyBBS');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>返信画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <h2>返信画面</h2>

    <!-- 選択した投稿の表示 -->
    <!-- 選択したidを取得するファイル(getPost.php)を実行する -->
    <?php require('getPost.php'); ?>

    <!-- 選択したidの投稿を表示 -->
    <table>
        <tr>
            <td>投稿者：<?php echo $result['post_name'] ?></td>
        </tr>
        <tr>
            <td>
                投稿時間：<?php echo $result['post_datetime'] ?>
                <hr>
            </td>
        </tr>
        <tr>
            <td style="height: 100px;"><?php echo $result['post_text'] ?></td>
        </tr>

    </table><br>


    <!-- 返信内容を入力するフォーム -->
    <form action="" method="POST">

        返信者<input type="text" name="reply_name" class="form-control"><br>
        返信内容<br>
        <textarea rows="3" cols="50" name="reply_text" class="form-control"></textarea><br>

        <div class="button1">
            <button type="submit" name="reply">返信</button>
            <button type="submit" name="cancel" formaction="/MyBBS">キャンセル</button>
        </div>

    </form>
    <br>

</body>

</html>

<?php
//データーベースの接続を切る
unset($dbh);
?>