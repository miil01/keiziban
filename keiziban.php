<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF=8">
        <title>mission3-5</title>
        </head>
        <body>
<?php
 //データベース登録
    $dsn='データベース名'; //データベースを設定
    $user = 'ユーザー名';
    $password = 'パスワード名';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //tbtest（テーブル名）がなかったら作る
    //青い文字実行してほしい文をSQL文という
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"//テーブルを作っている　IF NOT EXISTSはもしテーブルなかったらつくる
    //投稿をされたデータベースを保存するテーブル
    ." ("

    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"//半角英数３２文字の文字列　日本語だと全角だから１６文字になる
    . "comment TEXT,"//TEXT　文章
    . "date TEXT,"//,は区切りになる
    . "pass char(32)"
    .");";
    $stmt = $pdo->query($sql);//データベースに送信をするquery→何もいじらずにそのまま実行　PDOクラス 
?>

<?php
//POSTに送信する
//書き込み

if (!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["pass"])){
   

    $name= $_POST["name"];
    $comment = $_POST['comment']; 
    $date = date("Y年m月d日 H時i分s秒");
    $pass = $_POST["pass"];
   
  
   
        //編集　書き換える
        if(!empty($_POST["hidden"])){
            //$sql ='SHOW TABLES';　//テーブルの名前を表示する
            //$result = $pdo -> query($sql);
                    $sql = $pdo -> prepare("update tbtest set name =:name,comment=:comment,date=:date where id=:id and pass=:pass");
                    //INSERT INTO　新しくデータを追加する
                    // prepareは書き換えて実行execute（実行）を一緒に使う
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);//PDOのやつはテーブルで設定したものと同じものでないといけない
                    $sql -> bindParam(':id', $id, PDO::PARAM_INT);//オレンジはSQL、水色はPHP
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);//bindParamSQL文を用意し、活用するものだけ書く
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                    $id=$_POST["hidden"];
                    $sql -> execute();//executeはsplを実行する
                } 
            

    if(empty($_POST["hidden"])){
    $sql = $pdo -> prepare("INSERT INTO tbtest (name,comment,date,pass) VALUES (:name,:comment,:date,:pass)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    $sql -> execute();
    }
}

//削除 フォームのすぐ下部に表示
if(!empty($_POST['deleteNo'])) {
    $pass2= $_POST["pass2"];
    $sql = 'delete from tbtest where id=:id and pass=:pass';//使うテーブルを指している
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);//INTは数字
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);//STRは文字
    $id  = $_POST['deleteNo'];
    $pass = $pass2;
    $stmt->execute();
    $passX =$stmt->rowCount();//削除した数が入っている　rowCountはfetchAllほぼ同じだけどちょっと違う　
    if($passX===0){
    //パスワードが違った時
      echo "パスワードが間違っています";  
    }
 } 


//編集番号が入っている時、
if(!empty($_POST["editnum"])) {
    $sql = 'SELECT * FROM tbtest where id=:id and pass=:pass';
    $stmt = $pdo->prepare($sql);//preparはSQLの変数を使う時
    $id=$_POST["editnum"];
        $pass3= $_POST["pass3"];
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':pass', $pass3, PDO::PARAM_STR);
            $stmt->execute();
  $results = $stmt->fetchAll();//fetchAll SQLで取得したものを全部求める
            $i=0;
            foreach($results as $row){
                $edipass=$row["pass"];
                $ediname=$row["name"];
                $edicomment=$row["comment"];
                $i++;//1ずつ増えていく
            }
            if($i===0){
                //パスワードが違った時
                  echo "パスワードが間違っています";  
                }
             } 

?>


<form action=""method="post">

<input type="text" name="name" value="<?php if(!empty($ediname)){echo $ediname;} ?>" placeholder="名前">
<input type="text" name="comment" value="<?php if(!empty($edicomment)){echo $edicomment;} ?>" placeholder="コメント">
<input type="password" name="pass"value = "<?php if(!empty($edipass)){echo $edipass;} ?>" placeholder="パスワード">
<input type="hidden" name="hidden" value="<?php if(!empty($_POST["editnum"])){echo $_POST["editnum"];} ?>">
<input type="submit"  value="送信" name="submit"><br>

<input type="number" name="deleteNo" placeholder="削除対象番号">
<input type="submit" name="delete" value="削除">
<input type="password" name="pass2" placeholder="パスワード"><br>

<input type="number" name="editnum" placeholder="編集対象番号">
<input type="submit" name="edit" value="編集">
<input type="password" name="pass3" placeholder="パスワード">

</form>

<?php
$sql = "select * from tbtest";// *の意味は全部　テーブル内を全部表示させる 複数表示は,をつける　*のところを表示させる
//select テーブルの中のデータを展開する
$stmt = $pdo -> query($sql);
$result = $stmt -> fetchAll();
foreach($result as $row){
    echo $row ["id"]." ".$row ["name"]." ".$row ["comment"]." ".$row ["date"]." ".$row ["pass"]."<br>";// . はechoを複数繋げる
}
?>
</body>
</html>