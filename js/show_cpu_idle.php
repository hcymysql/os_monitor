    <script type="text/javascript">
              var  myChart = echarts.init(document.getElementById('cpu_idle'));
              var arr1=[],arr2=[];
              function arrTest(){
                $.ajax({
                  type:"post",
                  async:false,
                  //url:"get_graph_data.php",
		  url:"get_data/db_cpu_idle_graph_getdata.php?fn=index&host=<?php echo $host;?>&tag=<?php echo $tag;?>",
                  data:{},
                  dataType:"json",
                  success:function(result){
                    if (result) {
                      for (var i = 0; i < result.length; i++) {
                          arr1.push(result[i].create_time);
			  arr2.push(result[i].cpu_idle);
                          //arr3.push(result[i].count);
                      }
                    }
                  }
                })
                return arr1,arr2;
              }
              arrTest();

              var  option = {
		    title: {
			text: 'cpu idle空闲使用率图表',
		        //backgroundColor: 'FFFFFF'
		    },
                    tooltip: {
			trigger: 'axis'
                        //show: true
                    },
                    legend: {
                       data:['cpu idle统计']
                    },
                    xAxis : [
                        {
                            type : 'category',
                            data : arr1
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value',
			    axisLabel: {
				formatter:'{value}%'
                            }
			}
                    ],

                    dataZoom: [
                          {   // 这个dataZoom组件，默认控制x轴。
                                //type: 'inside',
                                type: 'slider', // 这个 dataZoom 组件是 slider 型 dataZoom 组件
                                //inverse: true,
                                start: 100,      // 左边在 10% 的位置。
                                end: 80         // 右边在 60% 的位置。
                           }
                    ],

                    grid:{
                            x2: 60 ,
                            bottom: "70px"
                    },

                    series : [
                        {
                            "name":"cpu idle统计",
                            //"type":"bar",
			    "type":"line",
			    "smooth": "true",
                            "data":arr2,
			    stack: '秒',
			    areaStyle: {
				normal: {
				    color: '#8cd5c2' //改变区域颜色
				}	
			    },
			    itemStyle : {
			    normal : { 
			        color:'#8cd5c2',
			        lineStyle: {
					color: '#3300FF',
					width: 3,
			    }}}
                        }
                    ]
                };
                // 为echarts对象加载数据
                myChart.setOption(option);
            // }
    </script>
