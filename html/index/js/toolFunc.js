
let toolFunc = {
  // 页面滚动头部动态效果
  headerScroll: function() {
    $(window).scroll(function() {
      if($(window).scrollTop() === 0) {
        $('body').removeClass('page-scroll');
      } else {
        $('body').addClass('page-scroll');
      }
    })
  },
  checkStr: function(param) {
    let isPass = true;
    let str = $(param.selStr).val();
    if(param.minLength) {
      if(this.getStrLength(str) < param.minLength) {
        isPass = !isPass;
      }
    }
    if(param.maxLength) {
      if(this.getStrLength(str) > param.maxLength) {
        isPass = !isPass;
      }
    }
    return isPass;
  },
  getStrLength: function(str) {
    return str.replace(/[^\u0000-\u00ff]/g,"aa").length
  }
};
