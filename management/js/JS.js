function arra(obj) {
    obj = obj || {};

    var type = (obj.type || 'get').toLowerCase();
    // type=type.toLowerCase();

    var url = obj.url || '';

    var data = obj.data || {};

    var success = obj.success || function () { };

    var error = obj.error || function () { };

    // 组装数据
        var arr=[];
        for(var i in data){
            arr.push(i+'='+data[i]);
        }
        var strData = arr.join('&');    

    var xhr = new XMLHttpRequest();

    if (type == 'get') {
        xhr.open(type, url + "?" + strData);
        xhr.send();
    }
    else {
        xhr.open(type, url);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(strData);
    }
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status >= 200 && xhr.status<300||xhr.send===304) {
                obj.success && obj.success(xhr.responseText);
            }
            else {
                obj.error && obj.error(xhr.error);
            }
        }
    }
}
