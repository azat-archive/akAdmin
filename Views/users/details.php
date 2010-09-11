<?=$this->content('header.php');?>
<ul class="form">
	<li><strong>Login</strong><?=$userInfo['login']?></li>
	<li><strong>Email</strong><?=e($userInfo['email'])?></li>
	<li><strong>Admin</strong><?=($userInfo['isAdmin'] ? 'x' : '&nbsp')?></li>
	<li><strong>Registration time</strong><?=dh('d.m.Y', $userInfo['createTime'])?></li>
	<?if ($userInfo['editTime']) {?><li><strong>Last edit time</strong><?=dh('d.m.Y', $userInfo['editTime'])?></li><?}?>
	<?if ($userInfo['lastTime']) {?><li><strong>Last visit time</strong><?=dh('d.m.Y', $userInfo['lastTime'])?></li><?}?>
	
	<?if ($user['isAdmin'] || $userInfo['id'] == $user['id']) {?>
	<li class="big">
		<strong>
			<nobr>
			<a href="/user/edit/<?=$userInfo['id']?>">Edit</a>
			<?if ($user['isAdmin']) {?>
			<a href="/user/grants/<?=$userInfo['id']?>">Grants</a>
			<a href="/user/erase/<?=$userInfo['id']?>" onclick="return false;" class="actionConfirm">Delete</a>
			<a href="#" onclick="duplicate.click('/user/duplicate/<?=$userInfo['id']?>/');">Copy</a>
			<?}?>
			</nobr>
		</strong>
	</li>
	<?}?>
</ul>
<?=$this->content('footer.php');?>