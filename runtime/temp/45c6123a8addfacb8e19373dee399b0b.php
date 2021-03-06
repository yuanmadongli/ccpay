<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:37:"../template/default/pay/recharge.html";i:1568044621;s:66:"/www/wwwroot/epay.3ii.cn/template/default/common/admin_header.html";i:1568086704;s:66:"/www/wwwroot/epay.3ii.cn/template/default/common/admin_footer.html";i:1568085444;}*/ ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?> - <?php echo get_sys('site_name'); ?></title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
	<meta http-equiv="Cache-Control" content="no-siteapp" />

	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="/static/admin/css/font.css">
	<link rel="stylesheet" href="/static/admin/css/xadmin.css">
	<link rel="stylesheet" href="/static/admin/css/public.css">
</head>
<body style="margin:20px;">
<article class="page-container">
    <form class="layui-form layui-form-pane">
        <div class="layui-form-item">
            <?php if(($types == 1)): ?>
            <label class="layui-form-label">充值金额：</label>
            <div class="layui-input-4">
                <input type="text" name="money" placeholder="请输入您要充值的金额！" class="layui-input">
            </div>
            <?php else: ?>
            <div class="layui-form-item">
                <label class="layui-form-label">升级套餐：</label>
                <div class="layui-input-block">
                    <input type="radio" name="level" value="1" checked title="基础版<?php echo get_sys('user_level1_price'); ?>元/月">
                    <input type="radio" name="level" value="2" title="标准版<?php echo get_sys('user_level2_price'); ?>元/月">
                    <input type="radio" name="level" value="3" title="高级版<?php echo get_sys('user_level3_price'); ?>元/月">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">升级时间：</label>
                <div class="layui-input-block">
                    <input type="radio" name="day" value="1" checked title="月">
                    <input type="radio" name="day" value="3" title="季">
                    <input type="radio" name="day" value="12" title="年">
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">支付方式：</label>
            <div class="layui-input-block">
                <?php if((get_sys('pay_qrcode_mode') == 0)): ?>
                <input type="radio" name="type" value="1" checked title="微信">
                <input type="radio" name="type" value="2" title="支付宝">
                <?php elseif((get_sys('pay_qrcode_mode') == 1)): ?>
                <input type="radio" name="type" value="1" checked title="微信">
                <?php else: ?>
                <input type="radio" name="type" value="2" checked title="支付宝">
                <?php endif; ?>
            </div>
        </div>
        <div class="layui-form-item qrcode" style="display:none;">
            <label class="layui-form-label">付款金额</label>
            <div class="layui-input-4">
                <input type="text" value="" id="price" disabled class="layui-input">
            </div>
        </div>
        <div class="layui-form-item qrcode" style="display:none;">
            <label class="layui-form-label">请扫码付款</label>
            <div class="layui-input-block">
                <div id="timeOut" style="color:red;font-size: 18px;"></div>
                <img src="">
                <input type="text" value="订单已超时！" id="timeTit" style="display: none;" disabled class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" lay-submit="" lay-filter="submit">立即<?php echo $types==1?"充值" : "升级/续费"; ?></button>
            </div>
        </div>
    </form>
</article>
<!--图片上传-->
<link rel="stylesheet" href="/static/lib/layui/css/layui.css">
<link rel="stylesheet" href="/static/lib/layui/css/layui.mobile.css">
<script type="text/javascript" src="/static/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/static/admin/js/xadmin.js"></script>
<script type="text/javascript" src="/static/lib/layui/layui.js"></script>
<script>
    orderid = "";
    layui.use(['form', 'layer'], function () {
        var form = layui.form, layer = layui.layer,$= layui.jquery;
        form.on('submit(submit)', function (data) {
            loading =layer.load(1, {shade: [0.1,'#fff']});
            $.post("", data.field, function (res) {
                layer.close(loading);
                var data = $.parseJSON(res.data);
                if (data.code == 1) {
                    layer.msg(data.msg, {time: 1500, icon: 1}, function () {
                        $('.qrcode img').attr('src','http://<?php echo get_sys('site_api'); ?>/enQrcode?url=' + data.data.payUrl);
                        $('#price').val(data.data.reallyPrice);
                        $('.qrcode').show();
                        $('.layui-btn').attr('disabled','disabled');
                        var time = new Date().getTime()-data.data.date*1000;
                        time = time/1000;
                        time = data.data.timeOut*60 - time;

                        if (data.state == -1){
                            time = 0;
                        }
                        orderid = data.data.orderId;
                        timer(time);
                        check();
                    });
                } else {
                    layer.msg(data.msg, {time: 1500, icon: 2});
                }
            });
        });
    });
    function formatDate(now) {
        now = new Date(now*1000)
        return now.getFullYear()
            + "-" + (now.getMonth()>8?(now.getMonth()+1):"0"+(now.getMonth()+1))
            + "-" + (now.getDate()>9?now.getDate():"0"+now.getDate())
            + " " + (now.getHours()>9?now.getHours():"0"+now.getHours())
            + ":" + (now.getMinutes()>9?now.getMinutes():"0"+now.getMinutes())
            + ":" + (now.getSeconds()>9?now.getSeconds():"0"+now.getSeconds());

    }
    var myTimer;
    function timer(intDiff) {
        var i = 0;
        i++;
        var day = 0,
            hour = 0,
            minute = 0,
            second = 0;//时间默认值
        if (intDiff > 0) {
            day = Math.floor(intDiff / (60 * 60 * 24));
            hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
            minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
            second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
        }
        if (minute <= 9) minute = '0' + minute;
        if (second <= 9) second = '0' + second;
        $('#timeOut').html('请在<s id="h"></s>' + minute + '分' + '<s></s>' + second + '秒');
        if (hour <= 0 && minute <= 0 && second <= 0) {
            qrcode_timeout()
            clearInterval(myTimer);

        }
        intDiff--;

        myTimer = window.setInterval(function () {
            i++;
            var day = 0,
                hour = 0,
                minute = 0,
                second = 0;//时间默认值
            if (intDiff > 0) {
                day = Math.floor(intDiff / (60 * 60 * 24));
                hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
            }
            if (minute <= 9) minute = '0' + minute;
            if (second <= 9) second = '0' + second;
            $('#timeOut').html('请在<s id="h"></s>' + minute + '分' + '<s></s>' + second + '秒内付款！');
            if (hour <= 0 && minute <= 0 && second <= 0) {
                qrcode_timeout()
                clearInterval(myTimer);

            }
            intDiff--;
        }, 1000);
    }

    function qrcode_timeout(){
        $('.qrcode img').hide();
        $('#timeTit').show();
    }
    function check() {
        $.post("/checkOrder","orderId="+orderid,function (data) {
            console.log(data);
            if (data.code == 1){
                window.location.href = data.data;
            } else{
                if (data.data == "订单已过期") {
                    intDiff = 0;
                }else{
                    setTimeout("check()",1500);
                }
            }
        })
    }
</script>
<!--_footer 作为公共模版分离出去-->

<!--/_footer 作为公共模版分离出去-->
</body>
</html>