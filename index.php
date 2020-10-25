<?php
//DB接続設定
     $dsn = 'mysql:dbname=データベース名;host=localhost';
     $user = 'ユーザー名';
     $password = 'パスワード';
     $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); 
     
    //  $stmt = $pdo -> prepare("DROP TABLE tbtest1");
    //  $stmt->execute();

     
     $sql = "CREATE TABLE IF NOT EXISTS tbtest1"
     ."("
     ."id INT AUTO_INCREMENT PRIMARY KEY,"
     ."name char(32),"//32文字の文字列で足りない部分は空白で埋まる
     ."comment TEXT,"
     ."password varchar(20),"
     ."date datetime"
     .");";
     $stmt = $pdo->query($sql);
     ?>
     
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
     
<?php
       
       $name = $_POST["name"];
       $com = $_POST["comment"];
       $date = date("Y/m/d H:i:s");
       $submit_pass =$_POST["submit_pass"];
       
       
      if(!empty($_POST["submit"])){//サブミットボタンが押されたとき
        
       if(empty($name)){//名前の欄が空だった時
        return;   
       }
       if(empty($com)){//コメント欄が空だっと時
           return;
       }
       if(empty($submit_pass)){//コメント欄が空だっと時
           echo "パスワードがありません"; return;
       }
       
       
       //サブミットボタンが押された→編集と新規作成かの分岐へ
       
       //新規ver
       if(empty($_POST["edit_num_confirm"])){//編集対象番号が空だった時
           
     $sql = $pdo -> prepare("INSERT INTO tbtest1 (name, comment, password, date) 
                             VALUES (:name, :comment, :password, :date)");
     $sql ->bindParam(':name', $name, PDO::PARAM_STR);
     $sql ->bindParam(':comment', $com, PDO::PARAM_STR);
     $sql ->bindParam(':password', $submit_pass, PDO::PARAM_STR);
     $sql ->bindParam(':date', $date, PDO::PARAM_STR);
     $sql -> execute();
       

       }else{//編集対象番号欄に数字が入っていた時

     $sql = 'UPDATE tbtest1 SET name =:name,comment=:comment, password=:password, date=:date WHERE id=:id';
     $stmt = $pdo->prepare($sql);
     $stmt->bindParam(':name',$name, PDO::PARAM_STR);
     $stmt->bindParam(':comment', $com, PDO::PARAM_STR);
     $stmt->bindParam(':id', $_POST["edit_num_confirm"], PDO::PARAM_INT);
     $stmt->bindParam(':password', $submit_pass, PDO::PARAM_STR);
     $stmt->bindParam(':date', $date, PDO::PARAM_STR);
     $stmt->execute(); 
          
         }
       
    //   削除機能
       }elseif(!empty($_POST["delete"])){//削除ボタンが押された時
         $delete_pass =$_POST["delete_pass"];
         
           if(empty($delete_pass)){//パスワードがなかった時
           echo "パスワードがありません。"; return;
           }
       
         $delete_num = $_POST["delete_num"];
         
         $sql = 'SELECT * FROM tbtest1 WHERE id=:id';
         $stmt = $pdo->prepare($sql);
         $stmt ->bindParam(':id',$delete_num, PDO::PARAM_INT);
         $stmt ->execute();
         $result = $stmt->fetchAll();
          if($result[0]['password']!==$delete_pass){ 
          $error= "パスワードが違います。<br>";
          
          }else{
                 

         $sql = 'delete from tbtest1 WHERE id=:id';
         $stmt = $pdo->prepare($sql);
         $stmt ->bindParam(':id',$delete_num, PDO::PARAM_INT);
         $stmt ->execute();
          } 
          
       //編集機能
       }elseif(!empty($_POST["edit"])){
           $edit_pass =$_POST["edit_pass"];
                if(empty($edit_pass)){//パスワードがなかった時
           echo "パスワードがありません。"; return;
       }
       
        $edit_num = $_POST["edit_num"];
        
        $sql = 'SELECT * FROM tbtest1 WHERE id=:id';
             $stmt = $pdo->prepare($sql);
             $stmt->bindParam(':id', $edit_num, PDO::PARAM_INT);
             $stmt ->execute();
             $result = $stmt->fetchAll();
             
             if($result[0]['password']!==$edit_pass){ 
          $error= "パスワードが違います。<br>";
          
          }else{
             $editting_name=$result[0]['name'];
             $editting_comment=$result[0]['comment'];
          }
             
       }
       
       ?>
       <?php echo $error;?>
    <form action=""method="post">
     <div> 
     <p>
    <input type="text" name="name" placeholder="名前" value="<?php
    echo $editting_name;
    ?>">
     </p>
     <p>       
    <input type="text" name="comment" placeholder="コメント" value="<?php
    echo $editting_comment;
    ?>">
    </p>
    <p>
    <input type="password" name="submit_pass" placeholder="パスワード">      
    <input type="hidden" name="edit_num_confirm" value="<?php
    echo $_POST["edit_num"];
    ?>">
     <input type="submit" name="submit">
     </p>
     </div>
     <div> 
     <p>
     <input type="text" name="delete_num" placeholder="削除対象番号"></p>
     <p>
     <input type="password" name="delete_pass" placeholder="パスワード">  
     <input type="submit" name="delete" value="削除">
     </p>
     </div>
     <div> 
     <p>
     <input type="text" name="edit_num" placeholder="編集対象番号"></p>
     <p>
     <input type="password" name="edit_pass" placeholder="パスワード">  
     <input type="submit" name="edit" value="編集">
     </p>
     </div>
    
    </form>
 <?php 
    $sql = 'SELECT * FROM tbtest1';
     $stmt = $pdo->query($sql);
     $results = $stmt->fetchAll();
     foreach($results as $row){
         echo $row['id'].' ';
         echo $row['name'].' ';
         echo $row['comment'].' ';
         echo $row['date'].'<br>';
        
        echo "<hr>"; 
     }  
?>
</body>
</html>