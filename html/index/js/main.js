// 页面头部滚动效果
toolFunc.headerScroll();


// 信息提交反馈
$('#msg-btn').click(function() {
  toolFunc.checkStr({
    selStr: '#msg-name',
    minLength: 1,
    maxLength: 20
  });
  toolFunc.checkStr({
    selStr: '#msg-mail',
    maxLength: 20
  });
  toolFunc.checkStr({
    selStr: '#msg-text',
    minLength: 1,
    maxLength: 100
  });
  console.log(toolFunc.checkStr({
    selStr: '#msg-name',
    minLength: 1,
    maxLength: 20
  }));
  return false;
})

$('div.nav-btn').click(function() {
  $(this).toggleClass('on');
  $('header.header').toggleClass('header-show');
})
