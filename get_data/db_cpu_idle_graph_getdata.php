<?php

function index($arr1,$arr2){
    ini_set('date.timezone','Asia/Shanghai');
    
/*
调试
    $host = '10.10.159.31';
    $tag = '测试机';
*/    
   
    $host = $arr1;
    $tag = $arr2;

    require '../conn.php';
 //   $get_info="select create_time,cpu_idle from os_status_history where host='${host}' and tag='${tag}' and create_time >=DATE_FORMAT(now(),'%Y-%m-%d')";
   $get_info="select create_time,cpu_idle from os_status_history where host='${host}' and tag='${tag}' ";
    $result1 = mysqli_query($conn,$get_info);
	//echo $get_info;

  $array= array();
  class Connections{
    public $create_time;
    public $cpu_idle;
  }
  while($row = mysqli_fetch_array($result1,MYSQL_ASSOC)){
    $cons=new Connections();
    $cons->create_time = $row['create_time'];
    //$user->user_max = $row['user_max'];
    $cons->cpu_idle = $row['cpu_idle'];
    $array[]=$cons;
  }
  $top_data=json_encode($array);
  // echo "{".'"user"'.":".$data."}";
 echo $top_data;
}

/*$fn = isset($_GET['fn']) ? $_GET['fn'] : 'main';
if (function_exists($fn)) {
  call_user_func($fn);
}
*/

    $host = $_GET['host'];
    $tag = $_GET['tag'];
   // $port = $_GET['port'];

index($host,$tag);


?>

