<?php 
session_start();
require('../library.php');
date_default_timezone_set("Asia/Tokyo");
$task_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$db = dbconnect();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $task = filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING);
    $date = strtotime(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_NUMBER_INT));
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    if (!$date){
        $date = 2147483647;
    }

    if(!$task){
        header('Location: edit.php?id='.$task_id);
        exit();
    }
    
    //カテゴリ登録
    $stmt = $db->prepare('
    INSERT INTO category (category_name) 
    SELECT ? 
    WHERE NOT EXISTS (
      SELECT * 
      FROM category 
      WHERE category_name = ?
    );');
    if(!$stmt){
        die($db->error);
    }
    $stmt->bind_param('ss',$category,$category);
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }

    //タスクアップデート
    $stmt = $db->prepare('
    UPDATE task SET task = ?,due_time = ?, category_id = (SELECT id FROM category WHERE category_name = ?)
    WHERE id = ?;
    ');
    if(!$stmt){
        die($db->error);
    }
    $stmt->bind_param('sisi',$task,$date,$category,$task_id);
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }


    header('Location: index.php?category_name='.$category);
    

}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスクの編集</title>
    <link rel="stylesheet" href="../css/style.css" type="text/css">

</head>
<body>

<div class="container">
    <h2>タスクの編集</h2>
    <?php
    $stmt = $db->prepare('
    SELECT task, category_name, t.id, t.category_id, due_time
    FROM task t, category c 
    WHERE t.category_id=c.id and t.id=?;');
    if(!$stmt){
        die($db->error);
    }
    $stmt->bind_param('i',$task_id);
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }
    $stmt->bind_result($task_view,$category_view,$task_id,$category_id,$due);
    $stmt->fetch();
    ?>
    <form action="" method="post">
        <input type="text" name="task" placeholder="新しいタスクを入力してください" value="<?php echo h($task_view);?>">
        <input type="datetime-local" name="date" value="<?php if($due){echo date('Y-m-d\TH:i',h($due));}?>"/>
        <input type="text" name="category" placeholder="カテゴリを入力してください" value="<?php echo h($category_view);?>">
        <input type="submit" style="background-color: gray;" value="編集完了"/>
    </form>

</div>

</body>
</html>
