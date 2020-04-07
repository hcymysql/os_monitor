# os_monitor 傻瓜式免安装-Centos操作系统资源监控工具
# 简介：
一款轻量级os系统可视化监控指标工具，采集的指标有cpu idle空闲使用率，cpu load负载使用率，内存使用率，磁盘空间使用率。

工作流程：Agent端从Server端os_status_info表中，获取被监控主机的各项系统阀值，采集客户端主机资源信息完成入库和报警，Server端用来监控客户端主机ssh是否存活和页面信息展示，可实现微信和邮件报警。

只需一条SQL，简单的配置，即可完成部署。


1、Dashboard首页
![image](https://raw.githubusercontent.com/hcymysql/os_monitor/master/demo_image/os_monitor_1.png)


点击图表，可以查看历史曲线图

2、cpu idle空闲使用率
![image](https://raw.githubusercontent.com/hcymysql/os_monitor/master/demo_image/os_monitor_2.png)


3、cpu load负载使用率
![image](https://raw.githubusercontent.com/hcymysql/os_monitor/master/demo_image/os_monitor_3.png)


4、内存使用率
![image](https://raw.githubusercontent.com/hcymysql/os_monitor/master/demo_image/os_monitor_4.png)


5、磁盘空间使用率
![image](https://raw.githubusercontent.com/hcymysql/os_monitor/master/demo_image/os_monitor_5.png)


