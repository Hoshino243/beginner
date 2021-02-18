<!Doctype html>
<html>
  <head>
   <meta charset = "UTF-8">
   <title>簡易掲示板</title>
  </head>
  <body>
    <?php
     
     $comment = "";
     $name = "";
     $pass = "";
     $delete = "";
     $delpass = "";
     $edit = "";
     $editpass = "";
     $flag = "";

     $name2 = "";
     $comment2 = "";
     $pass2 = "";
     $number2 = "";
        //DB接続
        
        $dsn= 'mysql:dbname=”DB名”;host="ホスト名";charset=utf8';
        $user="ユーザー名";
        $password="パスワード";
   
         try{
            $pdo = new PDO($dsn, $user, $password,
            array(
              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_EMULATE_PREPARES => false,
            )
          );
        }catch(PDOException $e){
          echo $e->getMessage();
        }

        //テーブル作成//
        $sql = "CREATE TABLE IF NOT EXISTS tb
           (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name char(32),
          comment TEXT,
          pass INT
          )";
        $stmt = $pdo->query($sql);
      
        /*編集モード→$edit=idの際の名前、コメントを取得　＋ 途中でifを使いパスワードの一致不一致で場合分け*/
        if($_POST["edit"] != null){
          $sql = $pdo -> prepare("SELECT * FROM tb WHERE id=:edit");
          $sql -> bindParam(':edit',$_POST["edit"],PDO::PARAM_INT);
          $sql -> execute();
          $stmt = $sql -> fetch(PDO::FETCH_ASSOC);//結果の行を連想配列の形で取得する。
         
          if($stmt["pass"] == $_POST["editpass"]){
            $name2 = $stmt["name"];
            $comment2 = $stmt["comment"];
            $pass2 = $stmt["pass"];
            $number2 = $_POST["edit"];
          }else{
             echo "パスワードが一致しません。";
          }
        }

     ?>
    <html>
      <form action="" method=post>
        <input type="text" name="comment" placeholder="コメント" value=<?php echo $comment2;?>>
        <input type="text" name="name" placeholder="名前" value=<?php echo $name2;?>>
        <input type="number" name="pass" placeholder="パスワード(数値で入力)" value=<?php echo $pass2; ?>>
        <input type="submit" name="submit" value="送信">

        <input type="number" name="delete" placeholder="削除対象番号" style='width:100px;'>
        <input type="number" name="delpass" placeholder="パスワード" >
        <input type="submit" name="submit2" value="削除">

        <input type="number" name="edit" placeholder="編集対象番号"  style='width:100px;'>
        <input type="number" name="editpass" placeholder="パスワード" >
        <input type="submit" name="submit3" value="編集">

        <input hidden = "number" name= "flag"  value=<?php echo $number2; ?>>
        <input hidden = "number" name="flag2" value=1>
      </form>
          
      <?php
        
        /*ボタンを押す前と押した後で分岐
        →未定義の変数を無くすため*/
      if($_POST["flag2"] != null){
         $comment = $_POST["comment"];
         $name = $_POST["name"];
         $pass = $_POST["pass"];
         $delete = $_POST["delete"];
         $delpass = $_POST["delpass"];
         $flag = $_POST["flag"];

         
         //編集機能+書き込み機能//
         
         
         if($comment != null){
          if($flag != null){
            $name3 = $_POST["name"];
            $comment3 = $_POST["comment"];
            $pass3 = $_POST["pass"];

            $sql = $pdo -> prepare ("UPDATE tb SET name=:name3,comment=:comment3,pass=:pass3
          WHERE id=:flag");
            $sql ->bindParam(':flag',$flag,PDO::PARAM_INT);
            $sql ->bindParam(':name3',$name3,PDO::PARAM_STR);
            $sql ->bindParam(':comment3',$comment3,PDO::PARAM_STR);
            $sql ->bindParam(':pass3',$pass3,PDO::PARAM_INT);
            
            $sql -> execute();

            echo "編集を受け付けました。";

          }else{
          $sql = $pdo -> prepare("INSERT INTO tb ( name,comment,pass) VALUES(:name,:comment,:pass)");
          $sql -> bindParam(':name',$name,PDO::PARAM_STR);
          $sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
          $sql -> bindParam(':pass',$pass,PDO::PARAM_INT);

          $sql -> execute();
          }
         }

        //削除機能//
         if($delete != null){
          $sql = $pdo -> prepare("SELECT * FROM tb WHERE id=:del");
          $sql -> bindParam(':del',$delete,PDO::PARAM_INT);
          $sql -> execute();
          $stmt = $sql -> fetch(PDO::FETCH_ASSOC);
          if($stmt != null){
            if($stmt["pass"] == $delpass ){
              $sql = $pdo -> prepare("DELETE from tb WHERE id = :del"); 
              $sql -> bindParam(':del',$delete,PDO::PARAM_INT);

              $sql -> execute();
            echo "削除を受け付けました。";
            }else{
           echo "パスワードが一致しません";
            }
        }else {echo "既に削除済みです";
        }
       
      } 

      }
        //表示機能//
        $sql = 'SELECT * FROM tb';
        $stmt = $pdo -> query($sql);
        $results = $stmt -> fetchall();
        foreach($results as $line){
          echo "<br>".$line['id'];
          echo "<br>".$line['name'];
          echo "<br>".$line['comment']."<hr>";
        }
      
      ?>
  </body>



</html>