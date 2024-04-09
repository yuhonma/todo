<?php
require('library.php');
$task_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
var_dump($task_id);

if(!$task_id){
    header('Location: index.php');
    exit();
}

$db = dbconnect();
$stmt = $db->prepare('UPDATE task SET completed = TRUE where id = ? limit 1');
if(!$stmt){
    die($db->error);
}
$stmt->bind_param('i',$task_id);
$success = $stmt->execute();
if(!$success){
    die($db->error);
}

header('Location: index.php');
exit();

?>