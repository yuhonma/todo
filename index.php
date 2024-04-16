<?php 
session_start();
require('library.php');
date_default_timezone_set("Asia/Tokyo");


$db = dbconnect();
$sort_types = [
    'desc' => 't.id desc',
    'asc' => 't.id asc',
    'category' => 't.category_id',
    'time' => 'due_time asc'
];

if(isset($_REQUEST["sort"])){
    $sort_type = $_REQUEST["sort"];
}else{
    $sort_type = "desc";
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $task = filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING);
    $date = strtotime(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    if (!$date){
        $date = 2147483647;
    }

    if(!$task){
        header('Location: index.php');
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

    //タスク登録
    $stmt = $db->prepare('
    INSERT INTO task (task,due_time,category_id)
    VALUES (?, ?, (SELECT id FROM category WHERE category_name = ?));
    ');
    if(!$stmt){
        die($db->error);
    }
    $stmt->bind_param('sis',$task,$date,$category);
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }

}
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
    <h2>ToDoリスト</h2>

    <form action="" method="post">
        <input type="text" name="task" placeholder="新しいタスクを入力してください">
        <input type="datetime-local" name="date" value=""/>
        <input type="text" name="category" placeholder="カテゴリを入力してください" value="">
        <input type="submit" value="追加"/>
    </form>

    <?php 
    $stmt = $db->prepare("
    SELECT task, category_name, t.id, t.category_id, due_time
    FROM task t, category c 
    WHERE t.category_id=c.id and t.completed = FALSE 
    ORDER BY $sort_types[$sort_type];");
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
        <form action="" method="GET">
            <select id="sort" name="sort" onchange="submit(this.form)">
                <option value="desc" <?php if($sort_type == "desc"){echo "selected";}?>>追加順(新しい)</option>
                <option value="asc" <?php if($sort_type == "asc"){echo "selected";}?>>追加順(古い)</option>
                <option value="category" <?php if($sort_type == "category"){echo "selected";}?>>カテゴリ順</option>
                <option value="time" <?php if($sort_type == "time"){echo "selected";}?>>期限順</option>
            </select>
        </form>

        <?php while($stmt->fetch()):?>
        <li>
            <span>
                <span class="task"><?php echo h($task_view);?></span>
                <a href="./category?category_name=<?php echo $category_view;?>" style="color: gray;"><?php echo h($category_view);?></a>
                <button onclick="location.href='do_complete.php?id=<?php echo $task_id;?>'" name="success">タスク完了</button>
            </span>
            <p class="time">
            <!-- 時間表示 -->
            <?php if($due != 2147483647):?>
            
                <?php echo h(view_deadline($due));?>
                [<?php echo h(view_time($due));?>]
            
            <?php endif; ?>
            
            <span class="menu">
                [<a href="edit.php?id=<?php echo $task_id;?>" style="color: gray;">編集</a>]
                [<a href="delete.php?id=<?php echo $task_id;?>" style="color: red;">削除</a>]
            </span>
            </p>
        </li>
        <?php endwhile; ?>
    </ul>
    <p name="completed"><a href="completed.php">完了したタスク</a></p>

</div>

</body>
</html>
