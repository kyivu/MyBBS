<!-- 選択された返信を検索 -->
<?php
try {
    $stmt = $dbh->prepare('SELECT * FROM replies WHERE reply_id = :id');
    $stmt->bindValue(':id', $_SESSION['rep_id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
} catch (Exception $e) {
    echo $e->getMessage();
}
