<?php
error_reporting(E_USER_WARNING | E_USER_NOTICE);
ini_set('date.timezone','Asia/Shanghai');
include 'mail/mail.php';
include 'weixin/weixin.php';
require 'conn.php';

// 主机存活检测
$check = new OS_check_alive;
$check->check_alive();

/*---------------------------------------------------------*/
class OS_check_alive {
	function check_alive(){
	    require 'conn.php';
        $result = mysqli_query($conn,"select host,ssh_port,tag,monitor,send_mail,send_mail_to_list,send_weixin,send_weixin_to_list from os_status_info");

	    if (!$result) {
    		printf("Error: %s\n", mysqli_error($conn));
   		    exit();
	    }

        while( list($host,$ssh_port,$tag,$monitor,$send_mail,$send_mail_to_list,$send_weixin,$send_weixin_to_list) = mysqli_fetch_array($result) ){

            if ($monitor == 0 || empty($monitor)) {
                echo "\n被监控主机：$host  【{$tag}】未开启监控，跳过不检测。" . "\n";
                exit;
            }

            //告警---------------------
            $is_alive = fsockopen($host, $ssh_port, $errno, $errstr, 10);
            if($errno==0){
                echo "被监控主机：$host  【{$tag}】主机访问正常。" . "\n";
                $sql_os_status = "REPLACE INTO os_status(host,tag,is_alive,create_time) VALUES('$host','$tag','online',now())";
            }

            if (!$is_alive) {
                if ($send_mail == 0 || empty($send_mail)) {
                    echo "被监控主机：$host  【{$tag}】关闭邮件监控报警。" . "\n";
                } else {
                    $alarm_subject = "【告警】被监控主机：" . $host . "  【{$tag}】" . "ssh无法连接，请检查。 " . date("Y-m-d H:i:s");
                    $alarm_info = "被监控主机：" . $host . "  【{$tag}】" . "ssh无法连接，请检查。错误信息： " . $errstr;
                    $sendmail = new mail($send_mail_to_list, $alarm_subject, $alarm_info);
                    $sendmail->execCommand();
                }

                if ($send_weixin == 0 || empty($send_weixin)) {
                    echo "被监控主机：$host  【$tag】关闭微信监控报警。" . "\n";
                } else {
                    $alarm_subject = "【告警】被监控主机：" . $host . "  【{$tag}】" . "ssh无法连接，请检查。 " . date("Y-m-d H:i:s");
                    $alarm_info = "被监控主机：" . $host . "  【{$tag}】" . "\"ssh无法连接，请检查。错误信息： " . $errstr;
                    $sendweixin = new weixin($send_weixin_to_list, $alarm_subject, $alarm_info);
                    $sendweixin->execCommand();
                }

                if (($send_mail == 1 || $send_weixin == 1)) {
                    $os_status = "UPDATE os_status_info SET alarm_alive_status = 1 WHERE host='$host'";
                    mysqli_query($conn, $os_status);
                }

                $sql_os_status = "REPLACE INTO os_status(host,tag,is_alive,create_time) VALUES('$host','$tag','offline',now())";
            } else {
                //恢复---------------------
                if ($send_mail == 0 || empty($send_mail)) {
                    echo "被监控主机：$local_host  【{$tag}】关闭邮件监控报警。" . "\n";
                }
                if ($send_weixin == 0 || empty($send_weixin)) {
                    echo "被监控主机：$local_host  【{$tag}】关闭微信监控报警。" . "\n";
                }
                if (($send_mail == 1 || $send_weixin == 1)) {
                    $recover_sql = "SELECT alarm_alive_status FROM os_status_info WHERE host='$host' and tag='$tag'";
                    $recover_status = mysqli_query($conn, $recover_sql);
                    $recover_status_row = mysqli_fetch_assoc($recover_status);
                }
                if (!empty($recover_status_row["alarm_alive_status"]) && $recover_status_row["alarm_alive_status"] == 1) {
                    $recover_subject = "【恢复】被监控主机：" . $host . "  【{$tag}】"  . "已恢复 " . date("Y-m-d H:i:s");
                    $recover_info = "被监控主机：" . $host . "  【{$tag}】" . "已恢复 ";
                    if ($send_mail == 1) {
                        $sendmail = new mail($send_mail_to_list, $recover_subject, $recover_info);
                        $sendmail->execCommand();
                    }
                    if ($send_weixin == 1) {
                        $sendweixin = new weixin($send_weixin_to_list, $recover_subject, $recover_info);
                        $sendweixin->execCommand();
                    }
                    $alarm_status = "UPDATE os_status_info SET alarm_alive_status = 0 WHERE HOST='$host'";
                    mysqli_query($conn, $alarm_status);
                }
            }

            if (mysqli_query($conn, $sql_os_status)) {
                echo "\n{$host}:'{$tag}' 监控数据采集入库成功\n";
            } else {
                echo "\n{$host}:'{$tag}' 监控数据采集入库失败\n";
                echo "Error: " . $sql_os_status . "\n" . mysqli_error($conn);
            }

        } //end while
    } //end usage()
} // end OS_check_detail

?>

