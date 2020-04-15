<?php
    ini_set('date.timezone','Asia/Shanghai');
?>

<!doctype html>
<html class="x-admin-sm">
<head>
    <meta http-equiv="Content-Type"  content="text/html;  charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="600" />  <!-- 页面刷新时间600秒 -->
    <title>系统资源 状态监控</title>

<style type="text/css">
a:link { text-decoration: none;color: #3366FF}
a:active { text-decoration:blink;color: green}
a:hover { text-decoration:underline;color: #6600FF}
a:visited { text-decoration: none;color: green}
</style>

    <script type="text/javascript" src="xadmin/js/jquery-3.3.1.min.js"></script>
    <script src="xadmin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="xadmin/js/xadmin.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="./css/font-awesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="./css/styles.css">

<script language="javascript">
function TestBlack(TagName){
 var obj = document.getElementById(TagName);
 if(obj.style.display=="block"){
  obj.style.display = "none";
 }else{
  obj.style.display = "block";
 }
}
</script>
</head>

<body>
<div class="card">
<div class="card-header bg-light">
    <h1><a href="os_status_monitor.php">系统资源 状态监控</a></h1>
</div>
      
<div class="card-body">
<div class="table-responsive">
                
<form action="" method="post" name="host_statement" id="form1" onsubmit=" return ss()">
  <div>
    <tr>
        <td><p align='left'>输入主机IP地址:
 	       <input type='text' name='host' value=''>	
            &nbsp;&nbsp;输入主机标签:
           <input type='text' name='tag' value=''>
		<td>
    </tr>
    <input name="submit" type="submit" class="STYLE3" value="搜索" />
    </label>
  </div>
</form>


<?php
echo "<table border='0' width='100%'>";
echo "<tr>";
echo "<td>监控采集阀值是每1分钟/次</td>";
echo "<td><p align='right'>最新监控时间:".date('Y-m-d H:i:s')."</td>";
echo "</tr>";
echo "</table>";
	
    if(isset($_POST['submit'])){
        $host_ip=$_POST['host'];
        $host_tag=$_POST['tag'];
    } 
?>

<table style='width:100%;font-size:14px;' class='table table-hover table-condensed'>                                    
<thead>                                   
<tr>                                                                         
<th>主机</th>
<th>标签</th>
<th>状态</th>
<th>cpu空闲使用率</th>
<th>cpu load负载</th>
<th>内存使用率</th>
<th>磁盘空间使用率</th>
<th>磁盘IO %util使用率</th>
<th>采集时间</th>
<th>图表</th>
</tr>
</thead>
<tbody>

<?php
require 'conn.php';



$perNumber=100; //每页显示的记录数  
$page=$_GET['page']; //获得当前的页面值  
$count=mysqli_query($conn,"select count(*) from os_status"); //获得记录总数
$rs=mysqli_fetch_array($count);   
$totalNumber=$rs[0];  
$totalPage=ceil($totalNumber/$perNumber); //计算出总页数  

if (empty($page)) {  
 $page=1;  
} //如果没有值,则赋值1

$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录 

    $condition.="1=1 ";	
    if(!empty($host_ip)){
    	$condition.="AND host='{$host_ip}'";
    }
    if(!empty($host_tag)){
    	$condition.="AND tag='{$host_tag}'";
    }
	$sql = "SELECT host,tag,is_alive,cpu_idle,cpu_load,memory_usage,disk_free,disk_io,create_time FROM os_status WHERE $condition order by id ASC LIMIT $startCount,$perNumber";
 	//echo $sql."<br>";   

$result = mysqli_query($conn,$sql);

//echo "复制监控采集阀值是每1分钟/次    最新监控时间：".date('Y-m-d H:i:s')."</br>";

while($row = mysqli_fetch_array($result)) 
{

$status=$row['is_alive']=='online'?'<b><span class="badge badge-success">在线</span></b>':'<span class="badge badge-danger">宕机</span>';

echo "<tr>";
echo "<td>{$row['host']}</td>";
echo "<td>{$row['tag']}</td>";
echo "<td>".$status."</td>";
echo "<td>{$row['cpu_idle']}</td>";
echo "<td>{$row['cpu_load']}</td>";
echo "<td>{$row['memory_usage']}</td>";
//echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('连接数详情','db_connect_statistic.php?ip={$row['1']}&dbname={$row['2']}&port={$row['3']}')\">{$row['7']}</a></td>";
echo "<td><pre><code>{$row['disk_free']}</code></pre></td>";
echo "<td><pre><code>{$row['disk_io']}</code></pre></td>";
echo "<td>{$row['create_time']}</td>";
echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('历史信息图表','show_graph.php?host={$row['host']}&tag={$row['tag']}')\"><img src='image/chart.gif' /></a></td>";
echo "</tr>";
}
//end while
echo "</tbody>";
echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

$maxPageCount=10; 
$buffCount=2;
$startPage=1;
 
if  ($page< $buffCount){
    $startPage=1;
}else if($page>=$buffCount  and $page<$totalPage-$maxPageCount  ){
    $startPage=$page-$buffCount+1;
}else{
    $startPage=$totalPage-$maxPageCount+1;
}
 
$endPage=$startPage+$maxPageCount-1;
 
 
$htmlstr="";
 
$htmlstr.="<table class='bordered' border='1' align='center'><tr>";
    if ($page > 1){
        $htmlstr.="<td> <a href='os_status_monitor.php?page=" . "1" . "'>第一页</a></td>";
        $htmlstr.="<td> <a href='os_status_monitor.php?page=" . ($page-1) . "'>上一页</a></td>";
    }

    $htmlstr.="<td> 总共${totalPage}页</td>";

    for ($i=$startPage;$i<=$endPage; $i++){
         
        $htmlstr.="<td><a href='os_status_monitor.php?page=" . $i . "'>" . $i . "</a></td>";
    }
     
    if ($page<$totalPage){
        $htmlstr.="<td><a href='os_status_monitor.php?page=" . ($page+1) . "'>下一页</a></td>";
        $htmlstr.="<td><a href='os_status_monitor.php?page=" . $totalPage . "'>最后页</a></td>";
 
    }
$htmlstr.="</tr></table>";
echo $htmlstr;

?>
