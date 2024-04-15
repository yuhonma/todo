<?php 
session_start();
require('../library.php');
date_default_timezone_set("Asia/Tokyo");
$db = dbconnect();
$category = filter_input(INPUT_GET, 'category_name', FILTER_SANITIZE_STRING);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDoリスト</title>
    <link rel="stylesheet" href="../css/style.css" type="text/css">

</head>
<body>

<div class="container">
    <h2>カテゴリ名"<?php echo h($category);?>"のタスク</h2>
    <?php 
    $stmt = $db->prepare('
    SELECT task, t.id , due_time
    FROM task t, category c 
    WHERE t.category_id=c.id 
    and t.completed = FALSE 
    and t.category_id = (select id from category where category_name = ?) 
    ORDER BY t.id desc;');

    if(!$stmt){
        die($db->error);
    }
    $stmt->bind_param('s',$category);
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }

    $stmt->bind_result($task_view,$task_id,$due);
    ?>

    <ul id="taskList">
        <a href="../index.php">トップに戻る</a>

        <?php while($stmt->fetch()):?>
        <li>
            <span name="task">
                <span style="font-size: 20px;"><?php echo h($task_view);?></span>
                <button onclick="location.href='do_complete.php?id=<?php echo $task_id;?>'" name="success">タスク完了</button>
            </span>
            
            <p>
            <?php if($due):?>
                <?php echo h(view_deadline($due));?>
                [<?php echo h(view_time($due));?>]
            <?php endif; ?>

            <span name="font-size: 13px;">
                [<a href="edit.php?id=<?php echo $task_id;?>" style="color: gray;">編集</a>]
                [<a href="delete.php?id=<?php echo $task_id;?>" style="color: red;">削除</a>]
            </span>
            </p>
        </li>
        <?php endwhile; ?>
    </ul>
    

</div>

</body>
</html>
