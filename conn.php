<?php         
     $conn = mysqli_connect("127.0.0.1","admin","hechunyang","os_monitor_db","3306") or die("数据库链接错误" . PHP_EOL .mysqli_connect_error());
     mysqli_query($conn,"set names utf8"); 
?>  
