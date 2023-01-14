"use strict";
exports.__esModule = true;
exports.post = exports.ajax = void 0;
var querystring = require("querystring");
function post(url, a, b) {
    if (typeof a == 'function') {
        ajax(url, 'POST', null, a);
    }
    else {
        ajax(url, 'POST', a, b);
    }
}
exports.post = post;
/**
 * 发送 POST 请求
 * @param url 请求地址
 * @param method 请求方式
 * @param data POST 数据
 * @param success 回调函数
 */
function ajax(url, method, data, success) {
    var xhr = new XMLHttpRequest();
    xhr.open(method, url);
    xhr.send(querystring.encode(data));
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            success(JSON.parse(xhr.responseText));
        }
    };
}
exports.ajax = ajax;