//自适应 
    window.onresize = function(){
        myChart.resize();
        }

//可修改数据
    var myChart = echarts.init(document.getElementById('ecm3'));
                option = {
                tooltip: {
                trigger: 'axis',
                axisPointer: { // 坐标轴指示器，坐标轴触发有效
                type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
                }
                },
                legend: {
                data: ['在线时间', '阅读反馈', '休息', '请假', '迟到', '旷工'],
                right: '40%'
                },
                calculable: true,
                xAxis: [{
                axisLabel: {
                rotate: 70,
                interval: 0
                },
                type: 'category',
                data: ["张琪","田雨欣","谭蕓漫","邓贵羊","张志威","符雨童","刘劲峰"]
                }],
                grid: { // 控制图的大小，调整下面这些值就可以，
                x: 40,
                x2: 80,
                y2: 125, // y2可以控制 X轴跟Zoom控件之间的间隔，避免以为倾斜后造成 label重叠到zoom上
                },
                yAxis: [{
                type: 'value'
                }],
                series: [{
                name: '在线时间',
                type: 'bar',
                barWidth: 10,
                stack: '总量',
                itemStyle: {
                normal: {
                label: {
                show: false,
                position: 'insideRight'
                },
                color: "#01c2b1"
                }
                },
                data: [10,9,8,5,6,4,2]
                }, {
                name: '阅读反馈',
                type: 'bar',
                barWidth: 10,
                stack: '总量',
                itemStyle: {
                normal: {
                label: {
                show: false,
                position: 'insideRight'
                },
                color: "#f6be1f"
                }
                },
                data: [2,5,4,5,8,6,4]
                },
                {
                name: '请假',
                type: 'bar',
                barWidth: 10,
                stack: '总量',
                itemStyle: {
                normal: {
                label: {
                show: false,
                position: 'insideRight'
                },
                color: "#ee6531"
                }
                },
                data: [2,5,4,5,8,4,5]
                }, {
                name: '迟到',
                type: 'bar',
                barWidth: 10,
                stack: '总量',
                itemStyle: {
                normal: {
                label: {
                show: false,
                position: 'insideRight'
                },
                color: "#b5b5b5"
                }
                },
                data: [2,0,1,0,1,2,0]
                }, {
                name: '旷工',
                type: 'bar',
                barWidth: 10,
                stack: '总量',
                itemStyle: {
                normal: {
                label: {
                show: false,
                position: 'insideRight'
                },
                color: "#c65885"
                }
                },
                data: [0,1,0,1,1,0,2]
                }
                ]
                };
                myChart.setOption(option); 
            