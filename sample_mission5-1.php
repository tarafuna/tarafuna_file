<!DOCTYPE html>
    <html lang="ja">
    <head>
    <meta charset="utf-8">
    <title>mission_5-1</title>
    </head>
    <body>
    <?php
        //全体の流れ
        /*  
        1データベース接続設定
        2データベース接続
        if-送信押されて中身がちゃんとあるとき、、
        送信されたものを変数にうけとる
        4データベースへ登録
        if削除が押されて中身がちゃんとある時、、
        5フォームから指定があった番号のデータを削除する
        if文、、
        6編集する機能を作る
        5データベース内を参照する（確認のために）
        5html内に＜投稿フォーム＞
                ＜削除フォーム＞
                ＜編集フォーム＞を書く
         */

        // データベース接続設定
        $dsn = 'データベース名';  // データソース名(半角スペース禁止)
	    $username = 'ユーザー名';
        $password = 'パスワード';
        $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);  // エラー表示を有効にする
        $pdo = new PDO($dsn, $username, $password, $options);

        $sql = 'CREATE TABLE IF NOT EXISTS table08(   /* @テーブル名というテーブルがなければ作成 */
            id INT(11) AUTO_INCREMENT PRIMARY KEY,    /* 自動連番の最大11桁の整数が入るカラム(プライマリーキーとして使用) */
            name VARCHAR(32),                         /* 最大32文字の文字列が入るカラム */
            comment TEXT,                             /* 文章用の長い文字列が入るカラム */
            time TEXT                                 /*　時間が表示できるカラム */
            )ENGINE=InnoDB default charset=utf8mb4';  /* 文字コードとデータベースエンジンの選択(省略可)  */
            /*  SQL文をqueryに渡して実行し、戻り値を$stmtに入れる
            queryメソッド：SQL文をPDOに渡して即時実行  */
            $stmt = $pdo -> query($sql);

        if(isset($_POST['submit'])){
            $name = $_POST['name']; 
            $comment = $_POST['comment'];
            date_default_timezone_set('Asia/Tokyo');
            $time = date("Y/m/d H:i:s");
            //準備をする
            $sql = $pdo -> prepare('INSERT INTO table08 (name, comment, time) VALUES (:name, :comment, :time)');
            // PDO::PARAM_STRでデータ型をstr型に指定して:nameに$nameを固定
	        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':time', $time, PDO::PARAM_STR);
            $sql -> execute();//実行する

            $sql = 'SELECT * FROM table08';  // @テーブル名からデータを戻り値として返させる
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();  // fetch()で戻り値から1行ずつデータを取得(All なら全部)！Allじゃだめじゃない？
            foreach ($results as $row){
            echo $row['id'].',';
            echo $row['name'].',<br>';
            echo $row['comment'].',<br>';
            echo $row['time'].'<br>';
            echo '<hr>';
            }
        }
            
        if(isset($_POST['delete'])){
            $deletenumber = $_POST['deletenumber'];//変数にポストされた内容を入れる
            $passkey = $_POST['pass'];//変数にポストされたパスワードを入れる
                if(isset($_POST['pass'])){
                    $id = $deletenumber;
                    $sql = 'delete from table08 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    $sql = 'SELECT * FROM table08';  // @テーブル名からデータを戻り値として返させる
	                $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();  // fetch()で戻り値から1行ずつデータを取得(All なら全部)！Allじゃだめじゃない？
	                foreach ($results as $row){
	    	        echo $row['id'].',';
	    	        echo $row['name'].',<br>';
                    echo $row['comment'].'<br>';
                    echo '<hr>';
                    }
                    }else{
                        $error2 = "パスワードの入力が必要です";
                        echo "$error2<br>";
                    }
        }
        if(isset($_POST['edit'])){//編集したい投稿番号が入力されたとき
            $edit = $_POST['editnumber'];
            if($edit != ''){                
            $id = $edit; //変更する投稿番号
            $name = $_POST['editname'];//ここどうすればいいんだろう、、
	        $comment = $_POST['editcomment']; //！入力した内容で変更させたいけど、、それを全て編集に入れたら何度も使える機能ではなくなるし！
	        $sql = 'UPDATE table08 SET name=:name,comment=:comment WHERE id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
                    
            $sql = 'SELECT * FROM table08';  // @テーブル名からデータを戻り値として返させる
	        $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();  // fetch()で戻り値から1行ずつデータを取得(All なら全部)！Allじゃだめじゃない？
	        foreach ($results as $row){
	        echo $row['id'].',';
	    	echo $row['name'].',<br>';
            echo $row['comment'].'<br>';
            echo '<hr>';
            }
        }else{
        $error3 = "番号の入力が必要です";
        echo "$error3<br>";
        }
        }
            
    ?>
    
    <form action=""method="post">
    【投稿】<br>
    <input type="txt" name="name" placeholder="名前">
    <input type="txt" name="comment" placeholder="コメント">
    <input type="txt" name="password" placeholder="パスワード">
    <input type="submit" name="submit" value="送信">
    <br>【削除】<br>
    <input type="number" name="deletenumber" placeholder="番号を入力">
    <input type="submit" name="delete" value="削除">
    <input type="txt" name="pass" placeholder="パスワード">
    <br>【変更】<br>
    <input type="number" name="editnumber" placeholder="番号を入力">
    <input type="text" name="editname" placeholder="変更後の名前">
    <input type="text" name="editcomment" placeholder="変更後のコメント">
    <input type="submit" name="edit" value="変更">


</form>
</body>
</html>
