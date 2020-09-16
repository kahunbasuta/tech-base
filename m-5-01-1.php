<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
    <?php
        $dsn='データベース名';
        $user='ユーザー名';
        $password='パスワード';
        $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));//arrayの後からはデータベース操作でエラーが発生した際に警告として表示するためのオプション
        //↑データベースへの接続

        //テーブル作成
        $sql="CREATE TABLE IF NOT EXISTS tbtest00"//ifnotexistsの意味はファイルが存在しないならでないと2回目以降エラーが発生（同じテーブルが2つできてしまうから）
        ."("
        . "id0 INT AUTO_INCREMENT PRIMARY KEY,"
        . "name0 char(32),"
        . "comment0 TEXT,"
        . "date0 char(32),"
        . "password0 char(32)"
        .");";
        $stmt=$pdo->query($sql);
        //テーブル作成完了


        //一覧
        $date=date("Y/m/d/H：i：s");
        if(!empty($_POST["name"])){$name=$_POST["name"];}//送信（名前）
        if(!empty($_POST["str"])){$str=$_POST["str"];}//送信（コメント）
        if(!empty($_POST["changenum"])){$changenum=$_POST["changenum"];}//編集番号
        if(!empty($_POST["editnum"])){$editnum=$_POST["editnum"];}//隠れ編集番号
        if(!empty($_POST["delenum"])){$delenum=$_POST["delenum"];}//削除番号
        if(!empty($_POST["subkey"])){$subkey=$_POST["subkey"];}//送信パスワード
        if(!empty($_POST["delkey"])){$delkey=$_POST["delkey"];}//削除パスワード
        if(!empty($_POST["editkey"])){$editkey=$_POST["editkey"];}//編集パスワード
        //一覧終了

        //投稿機能
        if(!empty($name) || !empty($str) || !empty($subkey) &&empty($editnum)){//送信ボタンが押された時
            if(!empty($name) && !empty($str) && !empty($subkey)&&empty($editnum)){//すべて埋まっている時
                $sql=$pdo->prepare("INSERT INTO tbtest00(name0,comment0,date0,password0)VALUES(:name0, :comment0, :date0, :password0)");
                $sql->bindParam(':name0',$name0, PDO::PARAM_STR);
                $sql->bindParam(':comment0',$comment0,PDO::PARAM_STR);
                $sql->bindParam(':date0',$date0,PDO::PARAM_STR);
                $sql->bindParam(':password0',$password0,PDO::PARAM_STR);
                $name0=$name;
                $comment0=$str;
                $date0=$date;
                $password0=$subkey;
                $sql->execute();
            }
            
        }
          //投稿機能終了

          //削除機能
          if(!empty($delenum)&&!empty($delkey)){//削除ボタンが押された時
            $id0=$delenum;
            //password00の設定
            $sql='SELECT*FROM tbtest00 WHERE id0=:id0';
            
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id0', $id0, PDO::PARAM_INT);
            $stmt->execute();
            
            $results=$stmt->fetchAll();
            foreach ($results as $row){
                $password0=$row['password0'];
            }
            //password00設定完了
            //delete実行
            $id0=$delenum;
            $sql='delete from tbtest00 where id0=:id0';
            $stmt =$pdo->prepare($sql);
            $stmt->bindParam('id0', $id0, PDO::PARAM_INT);

                if($password0==$delkey){
                    $stmt->execute();
                }
                else{
                    echo "パスワードが違います";
                }
          }
            //delete実行終了
            //削除機能終了
            
            //編集選択機能
            if(!empty($changenum)&&!empty($editkey)){
                $id0=$changenum;
                $sql='SELECT*FROM tbtest00 where id0=:id0';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id0', $id0, PDO::PARAM_INT);
                $stmt->execute();
                $results=$stmt->fetchAll();
                foreach ($results as $row){
                    $password0=$row['password0'];
                    $name0=$row['name0'];
                    $comment0=$row['comment0'];
                }
                if($editkey==$password0){
                    $name2=$name0;
                    $str2=$comment0;
                }
                else{//パスワードが誤っている時
                    echo"パスワードが間違っています";
                }
            }
            //編集選択機能終了
            //編集実行機能
            if(!empty($name) && !empty($str) && !empty($subkey)&& !empty($editnum)){
            $id0=$editnum;//変更する投稿番号
            $name0=$name;
            $comment0=$str;//変更したい名前、コメントは自分で決める
            $sql='UPDATE tbtest00 SET name0=:name0,comment0=:comment0 WHERE id0=:id0';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':name0',$name0,PDO::PARAM_STR);
            $stmt->bindParam(':comment0',$comment0,PDO::PARAM_STR);
            $stmt->bindParam(':id0',$id0,PDO::PARAM_INT);
            $stmt->execute();
            }
            //編集実行機能終了
    ?>
          <!--フォーム欄-->
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前を入力してください" value="<?php if(!empty($name2)){echo$name2;}?>"><br>
        <input type="text" name="str" placeholder="コメントを入力して下さい" value="<?php if(!empty($str2)){echo$str2;}?>"><br>
        <input type="text" name="subkey" placeholder="パスワードを入力してください">
        <input type="hidden" name="editnum" value="<?php if(!empty($changenum)){echo$changenum;}?>">
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <input type="number" name="delenum" placeholder="削除番号を入力してください">
        <input type="text" name="delkey" placeholder="パスワードを入力してください">
        <input type="submit" name="delete" value="削除">
    </form>
    <form action="" method="post">
        <input type="number" name="changenum" placeholder="編集番号を入力してください">
        <input type="text" name="editkey" placeholder="パスワードを入力してください">
        <input type="submit" name="edit" value="編集">
    </form>
    <!--フォーム欄終了-->

<?php
          //表示機能
        $sql='SELECT*FROM tbtest00';
        $stmt=$pdo->query($sql);
        $results=$stmt->fetchAll();
        foreach ($results as $row){
            //rowの中には4－2で作成したテーブルのカラム名が入る
            echo $row['id0'].',';
            echo $row['name0'].',';
            echo $row['comment0'].',';
            echo $row['date0'].',';
            echo $row['password0'].'<br>';
            echo"<hr>";
        }
          //表示機能終了
?>

</body>
</html>