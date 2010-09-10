<?=$this->content('header.php');?>
<form name="edit" method="post">
	<ul class="form">
		<li><label><strong>Login</strong><input type="text" name="login" value="<?=gfs((isset($_POST['login']) ? $_POST['login'] : null), $userInfo['login'])?>" /></label></li>
		<li><label><strong>Password</strong><input type="password" name="password" /></label></li>
		<li><label><strong>Email</strong><input type="text" name="email" value="<?=gfs((isset($_POST['email']) ? $_POST['email'] : null), $userInfo['email'])?>" /></label></li>
		
		<?if ($user['isAdmin']) {?>
		<li><label><strong>Admin</strong><input type="checkbox" name="isAdmin"<?=(gfs((isset($_POST['isAdmin']) ? $_POST['isAdmin'] : null), $userInfo['isAdmin']) ? ' checked' : false)?> /></label></li>
		<?}?>
		
		<li><input type="submit" value="Submit" /></li>
	</ul>
</form>
<?=$this->content('footer.php');?>