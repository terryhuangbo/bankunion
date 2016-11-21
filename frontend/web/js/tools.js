/**
 * Utilities Of JS
 * Created by Huangbo
 *
 **/
(function ($) {

    //提示框Alert
    $._alert = function (title, msg) {
        _generate_html._alert_html(title, msg);
        _btn_ok();
        _btn_no(); 
    };

    //确认框Confirm
    $._confirm = function (title, msg, callback) {
        _generate_html._confirm_html(title, msg);
        _btn_ok(callback);
        _btn_no();
    };
  
    //生成HTML
    var _generate_html = {
        _alert_html : function (title, msg) {
            var _html = "";
            _html += '<div id="mb_box"></div><div id="mb_con"><span id="mb_tit">' + title + '</span>';
            _html += '<a id="mb_ico">x</a><div id="mb_msg">' + msg + '</div><div id="mb_btnbox">';
            _html += '<input id="mb_btn_ok" type="button" value="确定" />';
            _html += '</div></div>';
            //必须先将_html添加到body，再设置Css样式
            $("body").append(_html);
            _generate_css._msg_css();
        },
        _confirm_html : function (title, msg, callback) {
            var _html = "";
            _html += '<div id="mb_box"></div><div id="mb_con"><span id="mb_tit">' + title + '</span>';
            _html += '<a id="mb_ico">x</a><div id="mb_msg">' + msg + '</div><div id="mb_btnbox">';
            _html += '<input id="mb_btn_ok" type="button" value="确定" />';
            _html += '<input id="mb_btn_no" type="button" value="取消" />';
            _html += '</div></div>';
            //必须先将_html添加到body，再设置Css样式
            $("body").append(_html);
            _generate_css._msg_css();
        },
        

    };

    //生成CSS
    var _generate_css = {
        _msg_css : function () {
            $("#mb_box").css({ width: '100%', height: '100%', zIndex: '99999', position: 'fixed',
                filter: 'Alpha(opacity=60)', backgroundColor: 'black', top: '0', left: '0', opacity: '0.6'
            });
            $("#mb_con").css({ zIndex: '999999', width: '400px', position: 'fixed',
                backgroundColor: 'White', borderRadius: '15px'
            });
            $("#mb_tit").css({ display: 'block', fontSize: '14px', color: '#444', padding: '10px 15px',
                backgroundColor: '#DDD', borderRadius: '15px 15px 0 0',
                borderBottom: '3px solid #009BFE', fontWeight: 'bold'
            });
            $("#mb_msg").css({ padding: '20px', lineHeight: '20px',
                borderBottom: '1px dashed #DDD', fontSize: '13px'
            });
            $("#mb_ico").css({ display: 'block', position: 'absolute', right: '10px', top: '9px',
                border: '1px solid Gray', width: '18px', height: '18px', textAlign: 'center',
                lineHeight: '16px', cursor: 'pointer', borderRadius: '12px', fontFamily: '微软雅黑'
            });
            $("#mb_btnbox").css({ margin: '15px 0 10px 0', textAlign: 'center' });
            $("#mb_btn_ok,#mb_btn_no").css({ width: '85px', height: '30px', color: 'white', border: 'none' });
            $("#mb_btn_ok").css({ backgroundColor: '#168bbb' });
            $("#mb_btn_no").css({ backgroundColor: 'gray', marginLeft: '20px' });

            //右上角关闭按钮hover样式
            $("#mb_ico").hover(function () {
                $(this).css({ backgroundColor: 'Red', color: 'White' });
            }, function () {
                $(this).css({ backgroundColor: '#DDD', color: 'black' });
            });

            var _widht = document.documentElement.clientWidth; //屏幕宽
            var _height = document.documentElement.clientHeight; //屏幕高
            var boxWidth = $("#mb_con").width();
            var boxHeight = $("#mb_con").height();

            //让提示框居中
            $("#mb_con").css({ top: (_height - boxHeight) / 2 + "px", left: (_widht - boxWidth) / 2 + "px" });
        },
    };

    //确定按钮事件
    var _btn_ok = function (callback) {
        $("#mb_btn_ok").click(function () {
            $("#mb_box,#mb_con").remove();
            if (typeof (callback) == 'function') {
                callback();
            }
        });
    };

    //取消按钮事件
    var _btn_no = function (callback) {
        $("#mb_btn_no, #mb_ico").click(function () {
            $("#mb_box, #mb_con").remove();
        });
        if (typeof (callback) == 'function') {
            callback();
        }
    };

    //获取表单数据
    $._get_form_json = function (form) {
        var o = {};
        var a = $(form).serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    //校验表单必填数据
    $._check_form = function (dom_name) {
        var _is_pass = true;
        $(dom_name).find('.required').each(function () {
            var _this_dom = $(this);
            var _value = _this_dom.val();
            if (_value == '' || _value == undefined) {
                _this_dom.parent().addClass('has-error');
                _is_pass = false;
            } else {
                _this_dom.parent().removeClass('has-error');
            }
        });
        return _is_pass;
    };

    //必填项处理
    $._form_notice_tips = function (dom_name) {
        $(dom_name).find('.required').each(function () {
            var _this_dom = $(this);
            _this_dom.on('blur', function () {
                var _value = $(this).val();
                if (_value == '' || _value == undefined) {
                    $(this).parent().addClass('has-error');
                } else {
                    $(this).parent().removeClass('has-error');
                }
            });
        });
    };

    //清除表单数据 includeHidden 是否包含hidden的输入框
    $.fn._clear_form = function(includeHidden) {
        return this.each(function() {
            $('input,select,textarea', this)._clear_fields(includeHidden);   //this表示设置上下文环境，有多个表单时只作用调用的表单
        });
    };

    //清除输入框数据
    $.fn._clear_fields = $.fn._clear_inputs = function(includeHidden) {
        var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
        return this.each(function() {
            var t = this.type;
            tag = this.tagName.toLowerCase();
            if (re.test(t) || tag == 'textarea') {
                this.value = '';
            }
            else if (t == 'checkbox' || t == 'radio') {
                this.checked = false;
            }
            else if (tag == 'select') {
                this.selectedIndex = -1;
            }
            else if (t == "file") {
                if (/MSIE/.test(navigator.userAgent)) {
                    $(this).replaceWith($(this).clone(true));
                } else {
                    $(this).val('');
                }
            }
            else if (includeHidden) {
                if ( (includeHidden === true && /hidden/.test(t)) ||
                    (typeof includeHidden == 'string' && $(this).is(includeHidden)) ) {
                    this.value = '';
                }
            }
        });
    };

    //临时生成表单并提交数据
    $._request_form = function(action, data, n) {
        var _form = $("<form/>").attr('action', action).attr('method', 'post');
        var _form = $("<form/>", {
            'action': action,
            'method': post,
        });
        if (n) {
            _form.attr('target', '_blank');
        }
        var input = '';
        $.each(data, function (i, n) {
            input += '<input type="hidden" name="' + i + '" value="' + n + '" />';
        });
        _form.append(input).appendTo("body").css('display', 'none').submit();
        _form.remove();
    }

    //ajax方式实现异步请求
    $._ajax = function (url, data, reqtype, rettype, callback) {
        if(url == '' || url == undefined){
            return
        }
        //防止重复提交相同请求
        var key = $._md5(url + JSON.stringify(data));
        if ($(document).data(key) == false) {
            return
        }
        $(document).data(key, false);
        $.ajax({
            url: url,
            data: data,
            type: reqtype,
            dataType: rettype,
            success: function (d) {
                //实现跳转
                if(d.data.redirectUrl){
                    window.location.href = d.data.redirectUrl;
                    return
                }
                if (typeof callback == "function") {
                    callback(d);
                }
            },
            complete: function(XHR, textStatus){
                $(document).data(key, true);
                //如果Reponse Header设置了X-Redirect参数，那实现跳转
                if (XHR.getResponseHeader('X-Redirect')) {
                    window.location.href = XHR.getResponseHeader('X-Redirect');
                }
            }
        });
    };

    //ajax方式实现异步跨域请求数据-jsonp
    $._jsonp = function (url, data, reqtype, callback) {
        if (url) {
            $.ajax({
                url: url,
                data: data,
                type: reqtype,
                dataType: 'jsonp',
                jsonp: 'callback',
                success: function (d) {
                    if (typeof callback == "function") {
                        callback(d);
                    }
                }
            });
        }
    };

    //判断str字符数量，中文-2，英文和字符-1
    $._str_len = function (str) {
        str = $.trim(str);
        var len = 0;
        for (var i = 0; i < str.length; i++) {
            if (str[i].match(/[^\x00-\xff]/ig) != null) //全角
                len += 2;
            else
                len += 1;
        }
        return len;
    }

    //返回str在规定字节长度max内的值
    $._str_cut = function (str, max) {
        str = $.trim(str);
        var value = '';
        var length = 0;
        for (var i = 0; i < str.length; i++) {
            if (str[i].match(/[^\x00-\xff]/ig) != null){
                length += 2;
            }
            else{
                length += 1;
            }
            if (length > max){
                break;
            }
            value += str[i];
        }
        return value;
    }

    //设置浏览器缓存
    $._cache = {
        set : function(k, v, expire){
            var h = (new Date).getTime();
            if (!k || !v) {
                return undefined;
            }
            ex = h + parseInt(expire * 1000);
            d = {
                data: v,
                timeStamp: ex
            };
            try {
                localStorage.setItem(k, JSON.stringify(d))
            } catch(exeption) {
                if (exeption.name === 'QuotaExceededError') {//超出本地存储限额
                    this.flush();
                    localStorage.setItem(k, JSON.stringify(d))
                }
            }
        },
        get : function(k){
            var b = localStorage.getItem(k);
            var c = (new Date).getTime();
            if (b) {
                b = JSON.parse(b) || {};
                if (b.timeStamp < c) {
                    this.remove(k);
                    return undefined;
                }
                return b.data;
            }
            return undefined;
        },
        remove : function(k){
            localStorage.removeItem(k)
        },
        flush : function(){
            localStorage.clear();
        },
    };

    //加载文件,可以用于临时性的引入js,css等文件，不必在header部分添加
    $._loader = function(){
        this.browser = {
            ie: /msie/.test(window.navigator.userAgent.toLowerCase()),
            moz: /gecko/.test(window.navigator.userAgent.toLowerCase()),
            opera: /opera/.test(window.navigator.userAgent.toLowerCase()),
            safari: /safari/.test(window.navigator.userAgent.toLowerCase())
        };
    };
    $._loader.prototype = {
        jsLoader: function (sUrl, callback) {
            self = this;
            var _script = document.createElement('script');
            _script.setAttribute('type', 'text/javascript');
            _script.setAttribute('charset', 'utf-8');
            _script.setAttribute('src', sUrl);
            document.getElementsByTagName('head')[0].appendChild(_script);
            if (self.browser.ie) {
                _script.onreadystatechange = function () {
                    if (this.readyState == 'loaded' || this.readyState == 'complete') {
                        callback();
                    }
                };
            }
            else if (self.browser.moz) {
                _script.onload = function () {
                    callback();
                };
            }
            else {
                callback();
            }
        },
    };

    $.fn._error = function( msg, location, fading){
        var dom = this;
        if(msg == undefined || msg == ''){
            return ;
        }
        if(fading == undefined){
            fading = 1500;
        }
        var error = $('<p class="msg-error">'+ msg +'</p>');
        if(location == undefined || location == 'after'){
            $(dom).siblings('p.msg-error').remove();
            $(dom).after(error);
        }else if(location == 'append'){
            $(dom).children('p.msg-error').remove();
            $(dom).append(error);
        }
        error.fadeOut(fading);
    }

    //随机字符串
    $._random_string = function (len) {
        var x = "0123456789qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        var tmp = "";
        var timestamp = new Date().getTime();
        for (var i = 0; i < len; i++) {
            tmp += x.charAt(Math.ceil(Math.random() * 100000000) % x.length);
        }
        return timestamp + tmp;
    };

    //MD5加密
    $._md5 = function (str) {
        var rotateLeft = function (lValue, iShiftBits) {
            return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
        };
        var addUnsigned = function (lX, lY) {
            var lX4, lY4, lX8, lY8, lResult;
            lX8 = (lX & 0x80000000);
            lY8 = (lY & 0x80000000);
            lX4 = (lX & 0x40000000);
            lY4 = (lY & 0x40000000);
            lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
            if (lX4 & lY4) return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
            if (lX4 | lY4) {
                if (lResult & 0x40000000) return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                else return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
            else {
                return (lResult ^ lX8 ^ lY8);
            }
        };
        var F = function (x, y, z) {
            return (x & y) | ((~x) & z);
        };
        var G = function (x, y, z) {
            return (x & z) | (y & (~z));
        };
        var H = function (x, y, z) {
            return (x ^ y ^ z);
        };
        var I = function (x, y, z) {
            return (y ^ (x | (~z)));
        };
        var FF = function (a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(F(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        };
        var GG = function (a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(G(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        };
        var HH = function (a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(H(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        };
        var II = function (a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(I(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        };
        var convertToWordArray = function (string) {
            var lWordCount;
            var lMessageLength = string.length;
            var lNumberOfWordsTempOne = lMessageLength + 8;
            var lNumberOfWordsTempTwo = (lNumberOfWordsTempOne - (lNumberOfWordsTempOne % 64)) / 64;
            var lNumberOfWords = (lNumberOfWordsTempTwo + 1) * 16;
            var lWordArray = Array(lNumberOfWords - 1);
            var lBytePosition = 0;
            var lByteCount = 0;
            while (lByteCount < lMessageLength) {
                lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                lBytePosition = (lByteCount % 4) * 8;
                lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
                lByteCount++;
            }
            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
            lBytePosition = (lByteCount % 4) * 8;
            lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
            lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
            lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
            return lWordArray;
        };
        var wordToHex = function (lValue) {
            var WordToHexValue = "", WordToHexValueTemp = "", lByte, lCount;
            for (lCount = 0; lCount <= 3; lCount++) {
                lByte = (lValue >>> (lCount * 8)) & 255;
                WordToHexValueTemp = "0" + lByte.toString(16);
                WordToHexValue = WordToHexValue + WordToHexValueTemp.substr(WordToHexValueTemp.length - 2, 2);
            }
            return WordToHexValue;
        };
        var uTF8Encode = function (string) {
            string = string.replace(/\x0d\x0a/g, "\x0a");
            var output = "";
            for (var n = 0; n < string.length; n++) {
                var c = string.charCodeAt(n);
                if (c < 128) {
                    output += String.fromCharCode(c);
                }
                else if ((c > 127) && (c < 2048)) {
                    output += String.fromCharCode((c >> 6) | 192);
                    output += String.fromCharCode((c & 63) | 128);
                }
                else {
                    output += String.fromCharCode((c >> 12) | 224);
                    output += String.fromCharCode(((c >> 6) & 63) | 128);
                    output += String.fromCharCode((c & 63) | 128);
                }
            }
            return output;
        };
        var x = Array();
        var k, AA, BB, CC, DD, a, b, c, d;
        var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
        var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
        var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
        var S41 = 6, S42 = 10, S43 = 15, S44 = 21;
        var string = uTF8Encode(str);
        x = convertToWordArray(string);
        a = 0x67452301;
        b = 0xEFCDAB89;
        c = 0x98BADCFE;
        d = 0x10325476;
        for (k = 0; k < x.length; k += 16) {
            AA = a;
            BB = b;
            CC = c;
            DD = d;
            a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
            d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
            c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
            b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
            a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
            d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
            c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
            b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
            a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
            d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
            c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
            b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
            a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
            d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
            c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
            b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
            a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
            d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
            c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
            b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
            a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
            d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
            c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
            b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
            a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
            d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
            c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
            b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
            a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
            d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
            c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
            b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
            a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
            d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
            c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
            b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
            a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
            d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
            c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
            b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
            a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
            d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
            c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
            b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
            a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
            d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
            c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
            b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
            a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
            d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
            c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
            b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
            a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
            d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
            c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
            b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
            a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
            d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
            c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
            b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
            a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
            d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
            c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
            b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
            a = addUnsigned(a, AA);
            b = addUnsigned(b, BB);
            c = addUnsigned(c, CC);
            d = addUnsigned(d, DD);
        }
        var tempValue = wordToHex(a) + wordToHex(b) + wordToHex(c) + wordToHex(d);
        return tempValue.toLowerCase();
    };


})(jQuery);