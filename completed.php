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
    <link rel="stylesheet" href="css/style.css" type="text/css">

</head>
<body>

<div class="container">
    <h2>完了したタスク</h2>

    <?php 
    $stmt = $db->prepare('
    SELECT task, category_name, t.id, t.category_id, due_time
    FROM task t, category c 
    WHERE t.category_id=c.id and t.completed = TRUE
    ORDER BY t.id desc;');
    if(!$stmt){
        die($db->error);
    }
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }

    $stmt->bind_result($task_view,$category_view,$task_id,$category_id,$due);
    ?>

    <ul id="taskList">
        <a href="index.php">トップに戻る</a>
        <?php while($stmt->fetch()):?>
        <li>
            <span name="task">
                <span class="task"><?php echo h($task_view);?></span>
                <a href="./category?category_name=<?php echo $category_view;?>" style="color: gray;"><?php echo h($category_view);?></a>
                <button onclick="location.href='do_incomplete.php?id=<?php echo $task_id;?>'" name="success">未完了タスクへ</button>
            </span>
            <p class="time">
            <!-- 時間表示 -->
            <?php if($due != 2147483647):?>
            
                <?php echo h(view_deadline($due));?>
                [<?php echo h(view_time($due));?>]
            
            <?php endif; ?>
            
            <span class="menu">
                [<a href="delete.php?id=<?php echo $task_id;?>" style="color: red;">削除</a>]
            </span>
            </p>
        </li>
        <?php endwhile; ?>
    </ul>
    

</div>

</body>
</html>
