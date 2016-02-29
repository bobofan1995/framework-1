<div class="login-top">
		<div class="login-packet">
			<div class="login-title">
				<p class="p-main">广州 · 多必格 · 网络科技有限公司</p>
				<p>Guangdong Dobigger Network Technology Co., Ltd.</p>
			</div>
		</div>
	</div>
	<div class="login-bottom">
		<div class="login-packet">
			<form action="">
				<div class="login-row">
					<span>用户名称：</span>
					<input type="text" placeholder="在这里输入用户名">
					<span>登录口令：</span>
					<input type="password" placeholder="在这里输入密码">
				</div>
				<div class="login-row">
					<input type="checkbox">
					<span class="remember">记住我</span>
					<input type="button" value="登录" onclick="login()">
				</div>
			</form>
		</div>
	</div>
<?php
$js = <<<js
<script>
function login(){
	alert("login...");
}
</script>
js;
?>