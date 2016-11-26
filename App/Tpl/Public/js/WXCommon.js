(function ($) {
    //设置默认值
    var defaults = {
        activityId: 0,
        isClickCount: 0,
        url: location.href,
        domainUrl: '',
        imgUrl: '',
        tittle: '',
        callBack: $.noop
    };
    //分享量统计
    var shareCount = function (activityID) {
        $.get("ShareCount", { ID: activityID }, function () {
        })
    }
    $.WXShare = function (options) {
        var options = $.extend(defaults, options);
        if (defaults.isClickCount == "1") {
            //页面统计数据
           /* $.get("/WeixinShare/clickCount", { ID: defaults.activityId }, function () {
            });*/
        }
        
        wx.config({
            debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: defaults.appId, // 必填，公众号的唯一标识
            timestamp: defaults.timestamp, // 必填，生成签名的时间戳
            nonceStr: defaults.nonceStr, // 必填，生成签名的随机串
            signature: defaults.signature, // 必填，签名，见附录1
            jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ']
        });
        
    	 
        wx.ready(function () {
            //分享到朋友圈
            wx.onMenuShareTimeline({
                title: defaults.tittle, // 分享标题
                link: defaults.domainUrl, // 分享链接
                imgUrl: defaults.imgUrl, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    shareCount(defaults.activityId);
                    if (defaults.callBack) {//判断是否需要回掉
                        defaults.callBack();
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享给朋友
            wx.onMenuShareAppMessage({
                title: defaults.tittle, // 分享标题
                desc: '', // 分享描述
                link: defaults.domainUrl, // 分享链接
                imgUrl: defaults.imgUrl, // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                    shareCount(defaults.activityId);
                    if (defaults.callBack) {//判断是否需要回掉
                        defaults.callBack();
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //分享到QQ
            wx.onMenuShareQQ({
                title: defaults.tittle, // 分享标题
                desc: defaults.tittle, // 分享描述
                link: defaults.domainUrl, // 分享链接
                imgUrl: defaults.imgUrl, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    shareCount(defaults.activityId);
                    if (defaults.callBack) {//判断是否需要回掉
                        defaults.callBack();
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
    }
})(jQuery);