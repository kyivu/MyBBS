<!-- 選択された投稿を検索 -->
<?php
try {
    $stmt = $dbh->prepare('SELECT * FROM posts WHERE post_id = :id');
    $stmt->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
} catch (Exception $e) {
    echo $e->getMessage();
}
