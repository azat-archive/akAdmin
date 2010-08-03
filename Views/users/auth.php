<?=$this->content('header.php');?>
<form name="auth" method="post">
	<ul class="form">
		<li><label><strong>Login</strong><input type="text" name="login" value="<?=gfs((isset($_POST['login']) ? $_POST['login'] : null))?>" /></label></li>
		<li><label><strong>Password</strong><input type="password" name="password" value="<?=gfs((isset($_POST['password']) ? $_POST['password'] : null))?>" /></label></li>
		<li>
			<label>
				<strong>Captcha</strong>
				<input type="text" name="captcha" autocomplete="off" />
				<img src="/captcha" alt="captcha" />
			</label>
		</li>
		
		<li><input type="submit" value="Submit" /></li>
	</ul>
</form>
<?=$this->content('footer.php');?>