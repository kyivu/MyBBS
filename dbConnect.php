<!-- データベース接続 -->
<?php
$dsn = "mysql:dbname=mrkmkrn_db;host=mysql1.php.starfree.ne.jp;charset=utf8";
$userName = "mrkmkrn_user";
$password = "Karin2835";

try {
    $dbh = new PDO($dsn, $userName, $password);  //PDOというクラスを用いて接続する
} catch (PDOException $e) {
    echo "エラーメッセージ : " . $e->getMessage(); //接続できなかった場合はエラーメッセージ表示する
}
