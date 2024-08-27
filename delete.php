<?php
//データベース接続を行うファイル(dbConnect.php)を実行する
require('dbConnect.php');

session_start();

//index.phpで削除ボタン押されたら、削除するidを取得する
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    session_start();
    $_SESSION['id'] = $id;
}


//削除確定ボタンを押されたら、削除するidを取得し、データベースの削除を実行
if (isset($_POST['delete'])) {

    try {
        $sql = "DELETE FROM posts WHERE post_id = :delete_id ";  //登録するSQL文を代入
        $stmt = $dbh->prepare($sql); //$sqlで定義されたSQL文がデータベースに送信され、実行の準備が整う
        $stmt->bindValue(':delete_id', $_SESSION['id'], PDO::PARAM_INT); //プレースホルダにバインド
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
    <title>削除画面</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <h2>こちらを削除してもよろしいですか？ </h2>

    <form action="" method="POST">

        <!-- 選択したidの投稿を取得するファイル(getId.php)を実行 -->
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
                <td><?php echo $result['post_text'] ?></td>
            </tr>
            <tr>
                <td>
                    <div class="button1">
                        <button type="submit" name="delete">削除確定</button>
                        <button type="submit" name="cancel" formaction="/MyBBS">キャンセル</button>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</body>

</html>

<?php
//データーベースの接続を切る
unset($dbh);
?>