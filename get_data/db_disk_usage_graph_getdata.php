<?php

function index($arr1,$arr2,$arr3,$arr4){
    ini_set('date.timezone','Asia/Shanghai');
    
/*
调试


    $host = '10.10.159.31';
    $tag = '测试机';
    $mount = $_GET['mount'];
*/    
   
    $host = $arr1;
    $tag = $arr2;
    $mount = $arr3;
    $interval_time = $arr4;

    require '../conn.php';
    $get_info="select create_time,disk_usage from os_disk_history where host='${host}' and tag='${tag}' and mount='${mount}'
               and create_time >=${interval_time} AND create_time <=NOW()";
    $result1 = mysqli_query($conn,$get_info);
	//echo $get_info;

  $array= array();
  class Connections{
    public $create_time;
    public $disk_usage;
  }
  while($row = mysqli_fetch_array($result1,MYSQL_ASSOC)){
    $cons=new Connections();
    $cons->create_time = $row['create_time'];
    $cons->disk_usage = $row['disk_usage'];
    $array[]=$cons;
  }
  $top_data=json_encode($array);
  // echo "{".'"user"'.":".$data."}";
  echo $top_data;

 }

    $mount = $_GET['mount'];
    $host = $_GET['host'];
    $tag = $_GET['tag'];
    $interval_time = $_GET['interval_time'];

index($host,$tag,$mount,$interval_time);


?>

