<?php
require('dbConnect.php'); //データベース接続を行うファイル(dbConnect.php)を実行する

//topページに戻ってきたら、セッションの関連するキーを削除する
if (isset($_SESSION)) {
    unset($_SESSION['id']);
    unset($_SESSION['rep_id']);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <!-- 文字化け対策　文字コードの指定 -->
    <meta charset="utf-8">

    <!-- デバイスごとにコンテンツの表示領域を設定する -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MyBBS</title>

    <!-- BootStrapの導入-->
    <!-- 別途ファイルをダウンロードしなくてもCDNという仕組みを利用 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <h2>MyBBS</h2>

    <!-- 新規投稿機能 -->

    <!-- 投稿　データベースの登録処理  -->
    <?php
    if (isset($_POST['post'])) { //新規投稿ボタンをクリックされたら //（name属性「post」がポスト送信されたら）
        $post_datetime = date('Y-m-d H:i:s'); //変数 "post_datetime"にクリックされた時間が代入される

        try { //trycatchで例外が発生した場合、表示させる
            $sql = "INSERT INTO posts SET post_name = ? ,post_text = ?,post_datetime = ?"; //登録するSQL文
            $stmt = $dbh->prepare($sql); //$sqlで定義されたSQL文がデータベースに送信され、実行の準備が整う
            //変数dbhに代入されたPDOクラスのprepareメソッドを実行
            $stmt->execute(array($_POST['post_name'],  $_POST['post_text'], $post_datetime)); //プレースホルダにセットして、実行
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    ?>

    <!-- 新規投稿入力箇所 -->
    <form action="" method="POST">
        投稿者<input type="text" name="post_name" class="form-control"><br>
        本文<br>
        <textarea rows="3" cols="50" name="post_text" class="form-control"></textarea><br>
        <div class="button1">
            <button type="submit" name="post" class="btn btn-warning">新規投稿</button>
        </div>
    </form>
    <br><br>

    <!-- 一覧表示機能 -->
    <!--データベースから投稿を降順で取得する処理-->
    <?php
    try {
        $sql = "SELECT * FROM posts ORDER BY post_id desc";
        $stmt = $dbh->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>

    <!--データベースから返信を降順で取得する処理-->
    <?php
    try {
        $sql = "SELECT * FROM replies ORDER BY reply_id desc";
        $stmt = $dbh->query($sql);
        $rep_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($dbh);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>

    <!-- ページネーションの準備＋投稿の表示 -->
    <?php
    //ページネーションの準備
    define('MAX', '3');   // 1ページの記事の表示数  //define()定義する
    $posts_num = count($result);  //データベースのデータのトータルの件数を取得 
    $max_page = ceil($posts_num / MAX); //トータルページ数を計算　//ceilは小数点を切り捨てる関数

    //ページ数の取得 
    //GET送信でpage_idのデータが送信されていなければ、= 変数page_idに値が設定されていなければ、
    if (!isset($_GET['page_id'])) {    // $_GET['page_id'] はURLに渡された現在のページ数
        $now = 1; // 設定されてない場合は1ページ目にする
    } else {
        $now = $_GET['page_id']; //設定されている場合は、設定されたpage_idを現在のページ数にする
    }

    $start_no = ($now - 1) * MAX; // 配列の何番目から取得すればよいかを算出

    $disp_data = array_slice($result, $start_no, MAX, true); //必要な分だけ配列を抽出
    // array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数


    //投稿の表示箇所 ※3件ずつ表示
    //$$disp_dataの配列を$pos_valに代入していく
    foreach ($disp_data as $pos_val): //foreach (反復可能なデータ構造=配列 as 要素)
    ?>
        <form action="" method="POST">
            <table>
                <tr>
                    <td>投稿者：<?php echo $pos_val['post_name'] ?></td>
                    <td>
                        <div class="button1">
                            <button type="submit" name="edit_id" value="<?php echo $pos_val['post_id'] ?>" formaction='edit.php'>編集</button>
                            <button type="submit" name="delete_id" value="<?php echo $pos_val['post_id'] ?>" formaction='delete.php'>削除</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        投稿時間：<?php echo $pos_val['post_datetime'] ?>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $pos_val['post_text'] ?></td>
                </tr>
                <tr>
                    <!-- 返信件数の表示 -->
                    <td colspan="2" style="text-align:right;">
                        <?php

                        //投稿と返信内容をpost_idで紐づけをし、返信件数の集計をする
                        $reply_num = 0;
                        foreach ($rep_result as $rep_val):
                            //投稿のpost_idと返信内容のpost_idが一致するものがあれば、
                            if ($pos_val['post_id'] == $rep_val['post_id']) {
                                $reply_num++; //カウントする
                            }
                        endforeach;

                        //返信件数が1件以上存在すれば、返信件数を表示し、リンクを貼る
                        if ($reply_num > 0) {
                            echo "<a href='/MyBBS/reply_detail.php?post_id=" . $pos_val['post_id'] . "'> 返信件数 (" . $reply_num . ") 件 </a>";
                        } else {
                            echo "返信件数(0)件"; //0件以下の場合は、リンクの表示なし
                        }
                        ?>
                        <button type="submit" name="reply_id" value="<?php echo $pos_val['post_id'] ?>" formaction=' reply.php'>返信</button>
                    </td>
                </tr>
            </table><br>
        </form>
    <?php
    endforeach;  //表示する投稿の　繰り返し出力を終了
    ?>

    <!-- ページネーションの表示 -->
    <div style="text-align:center">
        <?php
        // 最大ページ数分リンクを作成
        for ($i = 1; $i <= $max_page; $i++) {
            if ($i == $now) { // 現在表示中のページ数の場合はリンクを貼らない
                echo $now . '　';
            } else {
                echo "<a href='/MyBBS?page_id=" . $i . "')>" . $i . '</a>' . '　';
            }
        }
        ?>
    </div>
</body>

</html>