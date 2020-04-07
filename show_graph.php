<?php

    $host = $_GET['host'];
    $tag = $_GET['tag'];

?>


<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>图形展示</title>    
    <script src="js/echarts.common.min.js"></script>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/shine.js"></script>
</head>
<body style="height: 100%; margin: 0">
    <div id="cpu_idle" style="height:400px"></div>
          <?php include 'js/show_cpu_idle.php';?> 

<br><br>

     <div id="cpu_load" style="height:400px"></div>
	      <?php include 'js/show_cpu_load.php';?> 

<br><br>

     <div id="memory_usage" style="height:400px"></div>
              <?php include 'js/show_memory_usage.php';?>

<br><br>

     <?php include 'js/show_disk_usage.php';?>

</body>
</html>

