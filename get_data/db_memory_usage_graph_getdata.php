<?php

function index($arr1,$arr2,$arr3){
    ini_set('date.timezone','Asia/Shanghai');
    
/*
调试

    $host = '10.10.159.31';
    $tag = '测试机';

*/
       
    $host = $arr1;
    $tag = $arr2;
    $interval_time = $arr3;

    require '../conn.php';
    $get_info="select create_time,memory_usage from os_status_history where host='${host}' and tag='${tag}' 
               and create_time >=${interval_time} AND create_time <=NOW()";
    $result1 = mysqli_query($conn,$get_info);
	//echo $get_info;

  $array= array();
  class Connections{
    public $create_time;
    public $memory_usage;
  }
  while($row = mysqli_fetch_array($result1,MYSQL_ASSOC)){
    $cons=new Connections();
    $cons->create_time = $row['create_time'];
    //$user->user_max = $row['user_max'];
    $cons->memory_usage = $row['memory_usage'];
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
    $interval_time = $_GET['interval_time'];

index($host,$tag,$interval_time);


?>

