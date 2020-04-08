<?php
error_reporting(E_USER_WARNING | E_USER_NOTICE);
ini_set('date.timezone','Asia/Shanghai');
include 'mail/mail.php';
include 'weixin/weixin.php';
require 'conn.php';

$local_ip="/sbin/ip addr |  grep -w 'inet'| egrep -v '127.0.0.1|/32' | grep 'brd' | awk '{ print $2}' | cut -d'/' -f1 | head -n 1";

exec("$local_ip",$output_local_ip,$return_local_ip);

if($return_local_ip!=0){
    die("获取本机IP失败，退出主程序。\n\n");
}

$local_host=$output_local_ip[0];

/*---------------------------------------------------------*/
$check_sysstat="/bin/rpm -qa | grep sysstat";
exec("$check_sysstat",$output_sysstat,$return_sysstat);

if($return_sysstat!=0){
    die("系统没有安装sysstat包，请yum install sysstat -y进行安装。\n\n");
}

//采集数据---------------------
$check = new OS_check_detail('cpu_idle');
$cpu_idle_return=$check->usage();
$cpu_idle=round($cpu_idle_return[0]);
//echo '$cpu_idle的返回值是：'.$cpu_idle."\n";

$check = new OS_check_detail('cpu_load');
$cpu_load_return=$check->usage();
$cpu_load=round($cpu_load_return[0]);
//echo '$cpu_load的返回值是：'.$cpu_load."\n";

$check = new OS_check_detail('memory_usage');
$memory_return=$check->usage();
$memory=round($memory_return[0]);
//echo '$memory的返回值是：'.$memory."\n";

$check = new OS_check_detail('disk_free');
$disk_free_return=$check->usage();

$disk_free=implode(PHP_EOL,$disk_free_return);
//echo '$disk_free的返回值是：'.$disk_free."\n";

echo "\n主机IP是：".$local_host."\n主机标签是：".$check->host_tag."\n";

// 磁盘历史数据入库
foreach ($disk_free_return as $v){
    $disk_tmp = explode(" ",$v);
    $Used = rtrim($disk_tmp[0],'%');
    $Mounted = $disk_tmp[1];
    $disk_history="INSERT INTO os_disk_history(host,tag,is_alive,mount,disk_usage,create_time) 
                   VALUES ('{$local_host}','{$check->host_tag}','online','{$Mounted}','{$Used}',NOW())";
    mysqli_query($conn, $disk_history);
}

//入库

    $sql = "REPLACE INTO os_status(host,tag,is_alive,cpu_idle,cpu_load,memory_usage,disk_free,create_time) VALUES ('{$local_host}','{$check->host_tag}','online','{$cpu_idle}','{$cpu_load}','{$memory}','{$disk_free}',NOW())"; 

    if (mysqli_query($conn, $sql)) {
        echo "\n监控数据采集入库成功!\n";
        $history_sql="INSERT INTO os_status_history(host,tag,is_alive,cpu_idle,cpu_load,memory_usage,disk_free,create_time) VALUES ('{$local_host}','{$check->host_tag}','online','{$cpu_idle}','{$cpu_load}','{$memory}','{$disk_free}',NOW())";
	mysqli_query($conn, $history_sql);
    } else {
        echo "Error: " . $sql . "   " . mysqli_error($conn);
    }
    
/*---------------------------------------------------------*/
class OS_check {
    public $check_para;

    function __construct($check_para){
	    $this->check_para = $check_para;
    }

    function usage(){
	    switch($this->check_para){
	    case 'cpu_idle':
            $check_cpu_idle="/usr/bin/sar -u 1 3 | grep 'Average' | awk '{print \$NF}'";
            exec($check_cpu_idle,$output_cpu_idle,$return_cpu_idle);
            return $output_cpu_idle;
		/*-----------------------------------------*/
        case 'cpu_load':
            $check_cpu_load="/usr/bin/sar -q 1 3 | grep 'Average' | awk '{print \$4}'";
            exec($check_cpu_load,$output_cpu_load,$return_cpu_load);
            return $output_cpu_load;
        /*-----------------------------------------*/
        case 'memory_usage':
            $os_version=exec("/bin/cat /etc/redhat-release|sed -r 's/.* ([0-9]+)\..*/\\1/'");
            //echo "系统版本是：".$os_version."\n";
            if($os_version==6){
                $check_memory_usage="/usr/bin/free -m | awk '/Mem:/ {total = $2;} /cache:/ {printf \"%d\\n\", $3 / total * 100}'";
            } else {
                $check_memory_usage="/usr/bin/free -m |awk '/Mem:/{total=$2; used=$3; printf \"%d\\n\", used/total*100}'";
            }
            exec($check_memory_usage,$output_memory_usage,$return_memory_usage);
	    return $output_memory_usage;
        /*-----------------------------------------*/
        case 'disk_free':
            $check_disk_free="/bin/df | awk '{if((\$NF!~/boot/ && \$1!~/tmpfs/) && NR>1){print \$(NF-1),\$NF}}'";
            exec($check_disk_free,$output_disk_free,$return_disk_free);
            return $output_disk_free;
	    default:
            die("error \n");
	    }	
    }
}

class OS_check_detail extends OS_check{
    public $os_output;
    public $host_tag;

	function usage(){
	    $this->os_output = parent::usage();

	    global  $local_host;

	    require 'conn.php';
	    $result = mysqli_query($conn,"select tag,monitor,send_mail,send_mail_to_list,send_weixin,send_weixin_to_list,threshold_alarm_{$this->check_para} from os_status_info where host='$local_host'");

	    if (!$result) {
    		printf("Error: %s\n", mysqli_error($conn));
   		exit();
	    }

	    list($tag,$monitor,$send_mail,$send_mail_to_list,$send_weixin,$send_weixin_to_list,$threshold_alarm)=mysqli_fetch_array($result);
	    $this->host_tag = $tag;	
	   
	    if($monitor==0 || empty($monitor)){
        	echo "\n被监控主机：$local_host  【{$tag}】未开启监控，跳过不检测。"."\n";
        	exit;
   	    }

    //告警---------------------
     foreach($this->os_output as $v) {
         if($this->check_para != 'disk_free'){
	        if($this->check_para == 'cpu_idle'){
	            $os_output = 100-round($v);
	        } else {
                $os_output = round($v);
	        }
         } else{
             $disk_tmp = explode(" ",$v);
             $os_output = $disk_tmp[0];
             $os_output_tmp = $disk_tmp[1];
         }

	if($this->check_para == 'cpu_idle'){
	     echo $this->check_para." 空闲使用率是：".round($v) ."%" ."\n";
	} else {
      	     echo $this->check_para.' '.$os_output_tmp.' 使用率是：'.$os_output."\n";
	}

        if (!empty($threshold_alarm) && $os_output > $threshold_alarm) {
             if ($send_mail == 0 || empty($send_mail)) {
                 echo "被监控主机：$local_host  【{$tag}】关闭邮件监控报警。" . "\n";
             } else {
                 $alarm_subject = "【告警】被监控主机：" . $local_host . "  【{$tag}】" . $this->check_para . "使用率超高，请检查。 " . date("Y-m-d H:i:s");
                 $alarm_info = "被监控主机：" . $local_host . "  【{$tag}】" . $this->check_para . "使用率是 " . $os_output . "，高于报警阀值{$threshold_alarm}";
                 $sendmail = new mail($send_mail_to_list, $alarm_subject, $alarm_info);
                 $sendmail->execCommand();
             }

             if ($send_weixin == 0 || empty($send_weixin)) {
                 echo "被监控主机：$local_host  【{$tag}】关闭微信监控报警。" . "\n";
             } else {
                 $alarm_subject = "【告警】被监控主机：" . $local_host . "  【{$tag}】" . $this->check_para . "使用率超高，请检查。 " . date("Y-m-d H:i:s");
                 $alarm_info = "被监控主机：" . $local_host . "  【{$tag}】" . $this->check_para . "使用率是 " . $os_output . "，高于报警阀值{$threshold_alarm}";
                 $sendweixin = new weixin($send_weixin_to_list, $alarm_subject, $alarm_info);
                 $sendweixin->execCommand();
             }

             if (($send_mail == 1 || $send_weixin == 1)) {
                 $os_status = "UPDATE os_status_info SET alarm_{$this->check_para}_status = 1 WHERE host='{$local_host}'";
                 mysqli_query($conn, $os_status);
             }
         } else {
             //恢复---------------------
             if ($send_mail == 0 || empty($send_mail)) {
                 echo "被监控主机：$local_host  【{$tag}】关闭邮件监控报警。" . "\n";
             }
             if ($send_weixin == 0 || empty($send_weixin)) {
                 echo "被监控主机：$local_host  【{$tag}】关闭微信监控报警。" . "\n";
             }
             if (($send_mail == 1 || $send_weixin == 1)) {
                 $recover_sql = "SELECT alarm_{$this->check_para}_status FROM os_status_info WHERE host='{$local_host}'";
                 $recover_status = mysqli_query($conn, $recover_sql);
                 $recover_status_row = mysqli_fetch_assoc($recover_status);
             }
             if (!empty($recover_status_row["alarm_{$this->check_para}_status"]) && $recover_status_row["alarm_{$this->check_para}_status"] == 1) {
                 $recover_subject = "【恢复】被监控主机：" . $local_host . "  【{$tag}】" . $this->check_para . "已恢复 " . date("Y-m-d H:i:s");
                 $recover_info = "被监控主机：" . $local_host . "  【{$tag}】" . $this->check_para . "已恢复，当前使用率是： " . $os_output;
                 if ($send_mail == 1) {
                     $sendmail = new mail($send_mail_to_list, $recover_subject, $recover_info);
                     $sendmail->execCommand();
                 }
                 if ($send_weixin == 1) {
                     $sendweixin = new weixin($send_weixin_to_list, $recover_subject, $recover_info);
                     $sendweixin->execCommand();
                 }
                 $alarm_status = "UPDATE os_status_info SET alarm_{$this->check_para}_status = 0 WHERE HOST='{$local_host}'";
                 mysqli_query($conn, $alarm_status);
             }
         }
     } // end foreach
	return $this->os_output;
    } //end usage()
} // end OS_check_detail

?>

