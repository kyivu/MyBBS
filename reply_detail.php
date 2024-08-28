<?php require('dbConnect.php'); //データベース接続を行うファイル(dbConnect.php)を実行する

session_start();

//index.phpで返信件数のリンクを押されたら、該当する投稿のidをセッションに登録する
if (isset($_GET["post_id"])) {
    $_SESSION['id']  = $_GET["post_id"];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <!-- 文字化け対策　文字コードの指定 -->
    <meta charset="utf-8">

    <!-- デバイスごとにコンテンツの表示領域を設定するため -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>返信詳細画面</title>

    <!-- BootStrapの導入　別途ファイルをダウンロードしなくてもCDNという仕組みを利用 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <h2>返信詳細</h2>
    <!-- 選択された投稿を表示 -->
    <form action="" method="POST">

        <!-- 選択したidの投稿を取得するファイル(getPost.php)を実行 -->
        <?php require('getPost.php');
        ?>

        <table style="border:solid 2px black;">
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
        </table>
    </form>

    <div style="text-align:right">
        <a href="/MyBBS">元に戻る</a>
    </div>
    <br><br>

    <!-- 返信の一覧表示機能 -->
    <!--返信を降順で取得する処理-->
    <?php
    try {
        $sql = "SELECT * FROM replies WHERE post_id = :post_id ORDER BY reply_id desc";
        $stmt = $dbh->prepare($sql); //$sqlで定義されたSQL文がデータベースに送信され、実行の準備が整う
        $stmt->bindValue(':post_id', $_SESSION['id'], PDO::PARAM_INT); //プレースホルダにバインド
        $stmt->execute(); //sql文が実行される
        $rep_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>

    <!-- ページネーションの準備＋返信の表示 -->
    <?php
    //ページネーションの準備
    define('MAX', '3');   // 1ページの記事の表示数  //define()定義する
    $posts_num = count($rep_result);  //データベースのデータのトータルの件数を取得 
    $max_page = ceil($posts_num / MAX); //トータルページ数を計算　//ceilは小数点を切り捨てる関数

    if (!isset($_GET['page_id'])) { // $_GET['page_id'] はURLに渡された現在のページ数
        $now = 1; // 設定されてない場合は1ページ目にする
    } else {
        $now = $_GET['page_id'];
    }

    // 配列の何番目から取得すればよいか
    $start_no = ($now - 1) * MAX;

    // array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数
    $disp_data = array_slice($rep_result, $start_no, MAX, true);

    //$stmtの配列を$resultに代入していく
    foreach ($disp_data as $pos_val): //foreach (反復可能なデータ構造=配列 as 要素)
    ?>
        <!-- 返信の表示箇所 -->
        <form action="" method="POST">
            <table>
                <tr>
                    <td>返信者：<?php echo $pos_val['reply_name'] ?></td>
                </tr>
                <tr>
                    <td>
                        返信時間：<?php echo $pos_val['reply_datetime'] ?>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $pos_val['reply_text'] ?></td>
                </tr>
                <tr>
                    <td>
                        <div class="button1">
                            <button type="submit" name="rep_edit_id" value="<?php echo $pos_val['reply_id'] ?>" formaction='reply_edit.php'>編集</button>
                            <button type="submit" name="rep_delete_id" value="<?php echo $pos_val['reply_id'] ?>" formaction='reply_delete.php'>削除</button>
                        </div>
                    </td>
                </tr>
            </table><br>
        </form>
    <?php
    endforeach;  //表示する返信の　繰り返し出力を終了
    ?>

    <div style="text-align:center">
        <?php
        // 最大ページ数分リンクを作成
        for ($i = 1; $i <= $max_page; $i++) {
            if ($i == $now) { // 現在表示中のページ数の場合はリンクを貼らない
                echo $now . '　';
            } else {
                echo "<a href='/MyBBS/reply_detail.php?page_id=" . $i . "'>" . $i . '</a>' . '　';
            }
        }
        ?>
    </div>
</body>

</html>

<?php
//データーベースの接続を切る
unset($dbh);
?>