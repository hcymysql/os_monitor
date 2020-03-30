    <?php   
      
     $conn = mysqli_connect("10.10.159.31","admin","hechunyang","sql_db","3306") or die("数据库链接错误".mysql_error());  
     mysqli_query($conn,"set names utf8"); 

/*
	$local_host='10.10.159.31'; 
	$result = mysqli_query($conn,"select id from os_status_info where host='${local_host}'");
$row=mysqli_fetch_array($result,MYSQLI_NUM);

if (!$result) {
    printf("Error: %s\n", mysqli_error($conn));
    exit();
}
*/
    ?>  
