<?php 
//htmlspecialcharsを短くする
function h($value){
    return htmlspecialchars($value, ENT_QUOTES);
}
//dbに接続
function dbconnect(){
    //db情報を入力
    $db = new mysqli('IP', 'Username','Password','DBName');
    if(!$db){
        die($db->error);
    }

    return $db;
}

function view_deadline($due){
    $deadline = '期限超過';
    if(time() < $due){
        $diff = $due - time();
        if($diff < 60){
            $deadline = 'あと'.$diff.'秒';
        }elseif ($diff < 60*60) {
            $deadline = 'あと'.floor($diff/60).'分'.($diff%60).'秒';
        }elseif ($diff < 60*60*24){
            $deadline = 'あと'.floor($diff/(60*60)).'時間'.(floor($diff/60)%60).'分';
        }else{
            $deadline = 'あと'.floor($diff/(60*60*24)).'日'.(floor($diff/(60*60))%24).'時間';
        }
    }
    return $deadline;
}

function view_time($time){
    $weeks = ['日','月','火','水','木','金','土'];
    return date("Y年n月j日",$time).'('.$weeks[date('w',$time)].')'.date("G:i",$time);
}
?>