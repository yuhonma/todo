<?php 
session_start();
require('library.php');
date_default_timezone_set("Asia/Tokyo");
$db = dbconnect();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDoリスト</title>
    <link rel="stylesheet" href="./style.css" type="text/css">

</head>
<body>

<div class="container">
    <h2>完了したタスク</h2>

    <?php 
    $stmt = $db->prepare('
    select task, category_name, t.id 
    from task t, category c 
    WHERE t.category_id=c.id and t.completed = TRUE 
    order by t.id desc;');
    if(!$stmt){
        die($db->error);
    }
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }

    $stmt->bind_result($task_view,$category_view,$task_id);
    ?>

    <ul id="taskList">
        <a href="index.php">トップに戻る</a>

        <?php while($stmt->fetch()):?>
        <li>
            <span name="task">
                <span style="font-size: 20px;"><?php echo h($task_view);?></span>
                <span style="color: gray;"><?php echo h($category_view);?></span>
                <button onclick="location.href='do_incomplete.php?id=<?php echo $task_id;?>'" name="success">未完了タスクにする</button>
            </span>
            
            <p>[<a href="delete.php?id=<?php echo $task_id;?>" style="color: red;">削除</a>]</p>
        </li>
        <?php endwhile; ?>
    </ul>
    

</div>

</body>
</html>
