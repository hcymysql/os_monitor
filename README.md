# os_monitor
傻瓜式免安装-Centos操作系统资源监控工具

工作流程：Agent端从Server端os_status_info表中，获取被监控主机的各项系统阀值，采集客户端主机资源信息完成入库和报警，Server端用来监控客户端主机ssh是否存活和页面信息展示。

只需一条SQL，简单的配置，即可完成部署。

