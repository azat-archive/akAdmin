<?=$this->content('header.php');?>
<form name="add" method="post">
	<ul class="form">
		<li><label><strong>Login</strong><input type="text" name="login" value="<?=gfs((isset($_POST['login']) ? $_POST['login'] : null))?>" /></label></li>
		<li><label><strong>Password</strong><input type="password" name="password" value="<?=gfs((isset($_POST['password']) ? $_POST['password'] : null))?>" /></label></li>
		<li><label><strong>Email</strong><input type="text" name="email" value="<?=gfs((isset($_POST['email']) ? $_POST['email'] : null))?>" /></label></li>
		<li><label><strong>Admin</strong><input type="checkbox" name="isAdmin"<?=gfs((isset($_POST['isAdmin']) ? ' checked' : null))?> /></label></li>
		
		<li><input type="submit" value="Submit" /></li>
	</ul>
</form>
<?=$this->content('footer.php');?>