window.onload = function () {
    //留言
   
    $('.liejian').eq(0).on('click', function () {
        var angle=$('.angle').css('display');
        var hidden=$('.hidden').css('display');

        $(this).toggleClass(function () {
            if (angle == 'none' && hidden == 'none') {
                $('.angle').css('display' , 'block');
                $('.hidden').css('display', 'block');
                $('.angle-1').css('display', 'none');
                $('.hidden-1').css('display', 'none');
                $('.icon-0').eq(0).attr('src','../img/a1.png');
                $('.icon-0').eq(1).attr('src','../img/b.png');                                                
                $('.right').css('opacity',0.3);
                return;
            }
            else {
                $('.angle').css('display', 'none');
                $('.hidden').css('display', 'none');
                $('.icon-0').eq(0).attr('src','../img/a.png');                
                $('.right').css('opacity',1);
                return;
            }
        })
    })
   

    //留言
    $('.news').find('a').on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','none');    
        $('.daily').css('display','none');   
        $('.liu').css('display','block'); 
        $('.picture').css('display','none');   
        $('.angle-1').css('display', 'none');
        $('.hidden-1').css('display', 'none');  
        $('.angle').css('display', 'none');
        $('.hidden').css('display', 'none');
        $('.right').css('opacity',1);  
        $('.icon-0').eq(0).attr('src','../img/a.png');                                      
        
    })

    $('.footer').find('p').on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','none');    
        $('.daily').css('display','none');   
        $('.liu').css('display','block'); 
        $('.picture').css('display','none');   
        $('.angle-1').css('display', 'none');
        $('.hidden-1').css('display', 'none');  
        $('.angle').css('display', 'none');
        $('.hidden').css('display', 'none');
        $('.right').css('opacity',1);  
        $('.icon-0').eq(0).attr('src','../img/a.png');                                      
        
    })

    //侧面导航切换
    $('.nav-0').eq(0).on('click',function(){
        $('.control').css('display','block');
        $('.data').css('display','none');    
        $('.daily').css('display','none'); 
        $('.liu').css('display','none');  
        $('.picture').css('display','none');                                                                                                                                                                                                                                                                                                                                                         
    })
    $('.nav-0').eq(1).on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','block');    
        $('.daily').css('display','none'); 
        $('.liu').css('display','none');  
        $('.picture').css('display','none');                                                                                                                                                                                                             
    })
    $('.nav-0').eq(2).on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','none');    
        $('.daily').css('display','block');   
        $('.liu').css('display','none');  
        $('.picture').css('display','none');                                                                           
    })
   
    $('.nav-0').eq(3).on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','none');    
        $('.daily').css('display','none');   
        $('.liu').css('display','none');                                                                      
        $('.picture').css('display','block'); 
    })
  
    $('.nav-0').eq(4).on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','none');    
        $('.daily').css('display','none');   
        $('.liu').css('display','block'); 
        $('.picture').css('display','none');  
    })

    //查看
    $('.hui').on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','none');    
        $('.daily').css('display','none');   
        $('.liu').css('opacity',0.2); 
        $('.picture').css('display','none');
        $('.look').css('display','block');

        
    })
    //退出
    $('.tui2').on('click',function(){
        $('.control').css('display','none');
        $('.data').css('display','none');    
        $('.daily').css('display','none');   
        $('.liu').css('opacity',1); 
        $('.picture').css('display','none');
        $('.look').css('display','none');
    })
};
