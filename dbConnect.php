<!-- データベース接続 -->
<?php
$dsn = "***********";
$userName = "*****";
$password = "*****";

try {
    $dbh = new PDO($dsn, $userName, $password);  //PDOというクラスを用いて接続する
} catch (PDOException $e) {
    echo "エラーメッセージ : " . $e->getMessage(); //接続できなかった場合はエラーメッセージ表示する
}
