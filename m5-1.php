<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
</head>

<body>


<?php     
//SQLに接続
  $dsn = 'mysql:dbname=データベース名;host=localhost';
  $user = 'ユーザー名';
  $password = 'パスワード';
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])) {
                if (empty($_POST['editNum'])) {
                    // 新規投稿
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $date = date("Y/m/d H:i:s");
                    $password = $_POST["password"];
                    $sql = "INSERT INTO keiziban (name, comment, date, password) VALUES (:name, :comment, :date, :password)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->execute();
                } else {
                    // 編集処理
                    $id = intval($_POST["editNum"]);
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $editpass = $_POST["password"]; // パスワードを変数に代入
                    $sql = 'UPDATE keiziban SET name=:name, comment=:comment WHERE id=:id AND password=:password';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':password', $editpass, PDO::PARAM_STR);
                    $stmt->execute();
                    $editpass = "";
                }
            }

            // 編集依頼の処理
            if (!empty($_POST["editSubmit"])) {
                $id = intval($_POST["edit"]);
                $editpass = $_POST["epass"];
                $sql = 'SELECT * FROM keiziban WHERE id=:id AND password=:password';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->bindParam(":password", $editpass, PDO::PARAM_STR);
                $stmt->execute();
                $editpass = "";
                $results = $stmt->fetchAll();
                foreach ($results as $row) {
                    $editNum = $row['id'];
                    $editName = $row['name'];
                    $editComment = $row['comment'];
                    $editpass = $row['password'];
                }
            }

            // 削除依頼の処理
            if (!empty($_POST["delSubmit"])) {
                $id = intval($_POST["delete"]);
                $deletepass = $_POST["dpass"];
                $sql = 'DELETE FROM keiziban WHERE id=:id AND password=:password';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':password', $deletepass, PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        ?>

        <h2>掲示板</h2>
        <form action="" method="POST">
             <input type="text" name="name" placeholder="名前"><br>
             <input type="text" name="comment" placeholder="コメント"><br>
             <input type="password" name="password" placeholder="パスワード"
                value="<?php echo isset($editpass) ? $editpass : ''; ?>">
            <input type="number" name="editNum" value="<?php echo isset($editNum) ? $editNum : ''; ?>">
            <input type="submit" name="submit" value="送信"><br>
        </form>

        <!-- 削除依頼フォーム -->
        <h2>削除</h2>
        <form action="" method="POST">
             <input type="number" name="delete" placeholder="削除対象番号"><br>
             <input type="text" name="dpass" placeholder="パスワード">
            <input type="submit" name="delSubmit" value="削除">
        </form>

        <!-- 編集依頼フォーム -->
        <h2>編集</h2>
        <form action="" method="POST">
         <input type="number" name="edit" placeholder="編集対象番号"><br>
        <input type="text" name="epass" placeholder="パスワード">
            <input type="submit" name="editSubmit" value="編集">
        </form>

        

            
            <?php
    
            $sql = 'SELECT * FROM keiziban';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                echo $row['id'] . ',';
                echo $row['name'] . ',';
                echo $row['comment'] . ',';
                echo $row['date'] . '<br>';
                echo "<hr>";
            }
            ?>
</body>

</html>