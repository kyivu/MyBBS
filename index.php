<!-- したい事　
 ・どこかの掲示板を参考にもっと見た目をよくしたい
 ・編集時間をどうするのか
 ・処理を分けたい
-->

<?php require('dbConnect.php'); //データベース接続を行うファイル(dbConnect.php)を実行する

session_start();
unset($_SESSION);

// if (isset($_SESSION['id'])) {
//     unset($_SESSION['id']);
// }

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <!-- 文字化け対策　文字コードの指定 -->
    <meta charset="utf-8">

    <!-- デバイスごとにコンテンツの表示領域を設定するため -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MyBBS</title>

    <!-- BootStrapの導入　別途ファイルをダウンロードしなくてもCDNという仕組みを利用 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <h2>MyBBS</h2>

    <!-- 新規投稿機能 -->

    <!-- 新規登録の処理  -->
    <?php
    // 疑問(!empty($_POST['post']))だとできない
    if (isset($_POST['post'])) { //新規投稿をクリックされたら（name属性「post」がポスト送信されたら）
        $post_datetime = date('Y-m-d H:i:s'); //変数 "post_datetime"にクリックされた時間が代入される

        try {         //trycatchで例外が発生した場合、表示させる
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
    </form><br><br>

    <!-- 一覧表示機能 -->
    <!--投稿を降順で取得する処理-->
    <?php
    try {
        $sql = "SELECT * FROM posts ORDER BY post_id desc";
        $stmt = $dbh->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>

    <!--返信を新しい順番で表示する処理 ☆☆-->
    <?php
    try {
        $sql = "SELECT * FROM replies ORDER BY reply_datetime desc";
        $stmt = $dbh->query($sql);
        $rep_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($dbh);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>


    <!-- ページネーション＋投稿の表示 -->
    <?php
    define('MAX', '3');   // 1ページの記事の表示数  //define()定義する

    $posts_num = count($result);  //データベースのデータのトータルの件数を取得 

    $max_page = ceil($posts_num / MAX); //トータルページ数を計算　//ceilは小数点を切り捨てる関数

    if (!isset($_GET['page_id'])) { // $_GET['page_id'] はURLに渡された現在のページ数
        $now = 1; // 設定されてない場合は1ページ目にする
    } else {
        $now = $_GET['page_id'];
    }

    // 配列の何番目から取得すればよいか
    $start_no = ($now - 1) * MAX;

    // array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数
    $disp_data = array_slice($result, $start_no, MAX, true);

    //$stmtの配列を$resultに代入していく
    foreach ($disp_data as $pos_val): //foreach (反復可能なデータ構造=配列 as 要素)
    ?>


        <!-- 投稿の表示箇所 -->
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
                    <td colspan="2">
                        <div style="text-align:right;">

                            <!-- 返信件数の表示 -->

                            <?php
                            //返信件数の集計
                            $count = 0;
                            foreach ($rep_result as $rep_val):
                                //投稿と返信内容を紐づけする
                                if ($pos_val['post_id'] == $rep_val['post_id']) {
                                    $count++;
                                }
                            endforeach;

                            if ($count > 0) {
                                echo "<a href='/MyBBS/reply_detail.php?post_id=" . $pos_val['post_id'] . "'> 返信件数 (" . $count . ") 件 </a>";
                            } else {
                                echo "返信件数(0)件";
                            }
                            ?>

                            <button type="submit" name="reply_id" value="<?php echo $pos_val['post_id'] ?>" formaction=' reply.php'>返信</button>
                        </div>
                    </td>
                </tr>
                <?php //ID確認用 echo $result['post_id']
                ?>
            </table><br>
        </form>
    <?php
    endforeach;  //反復終了
    ?>

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