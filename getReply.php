<!-- 選択された投稿を検索 -->

<?php
session_start();

try {
    $stmt = $dbh->prepare('SELECT * FROM replies WHERE reply_id = :id');
    $stmt->bindValue(':id', $_SESSION['rep_id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
} catch (Exception $e) {
    echo $e->getMessage();
}
