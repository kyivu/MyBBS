<?php
//データベース接続を行うファイル(dbConnect.php)を実行する
require('dbConnect.php');

//index.phpで編集ボタン押されたら、編集するidを取得する
if (isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
}

//編集ボタンを押されたら、　
if (isset($_POST['edit'])) {

    //編集する各情報を取得
    $edit_id = $_POST['edit'];
    $new_post_name = $_POST['new_post_name'];
    $new_post_text = $_POST['new_post_text'];

    try {
        $sql = "UPDATE posts SET post_name = :new_post_name, post_text = :new_post_text WHERE post_id = :edit_id ";  //登録するSQL文を代入
        $stmt = $dbh->prepare($sql); //$sqlで定義されたSQL文がデータベースに送信され、実行の準備が整う
        $stmt->bindValue(':new_post_name', $new_post_name, PDO::PARAM_STR);
        $stmt->bindValue(':new_post_text', $new_post_text, PDO::PARAM_STR);
        $stmt->bindValue(':edit_id', $edit_id, PDO::PARAM_STR);
        $stmt->execute(); //sql文が実行される

        echo "編集実行";
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
    <title>編集画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>

    <h2>編集画面</h2>

    <form action="" method="POST">

        <!-- 選択したidを取得するファイル(getId.php)を実行する -->
        <?php require('getPost.php'); ?>

        <!-- 検索した編集する投稿の情報を表示する -->
        投稿者<input type="text" name="new_post_name" value="<?php echo $result['post_name'] ?>" class="form-control"><br>
        本文<br>
        <textarea rows="3" cols="50" name="new_post_text" class="form-control"><?php echo $result['post_text'] ?></textarea><br>

        <div class="button1">
            <button type="submit" name="edit" value="<?php echo $result['post_id'] ?>">編集</button>
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