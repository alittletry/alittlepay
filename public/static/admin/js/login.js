// 账号登录
function btnLogin() {
    if ($("#username").val() == "") {$(".has-username").addClass("has-error");return false;}
    if ($("#password").val() == "") {$(".has-password").addClass("has-error");return false;}
    if ($("#captchas").val() == "") {$(".has-captchas").addClass("has-error");return false;}
    $.post(url="/admin/login/verify",data={account:$("#username").val(),pwd:$("#password").val(),verify:$("#captchas").val()},function (res) {
        if (res.status == 200) window.location = "/admin/index/index";
        else alert(res.msg);
        return true;
    });
    $("#captcha").attr("src",'/admin/login/captcha?d='+Math.random());
    return false;
}