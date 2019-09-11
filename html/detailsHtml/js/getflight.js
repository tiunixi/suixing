/* 用ajax八航班数据传到前端
$('.TAG-4').click(function(){
    $.ajax({
        url:'indexMessage/checkflight.php',
        dataType:'json',
        type:'GET',
        data:{
            'flight':1,
            'startCity':"北京",
            'toCity':"上海",
        },
        success:function(data){
            
            console.log(data);
        }
    })
}) */