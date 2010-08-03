<?=$this->content('header.php');?>
<table>
	<tr>
		<td>Login</td>
		<td width="1%">Admin</td>
		<td width="1%"><nobr>Last visit</nobr></td>
		<td width="1%">Actions</td>
	</tr>
	<?$i = 0; foreach ($users as &$item) { $i++ ?>
	<tr>
		<td class="class<?=($i % 2)+1?>"><a href="/user/details/<?=$item['id']?>"><?=$item['login']?></a></td>
		<td class="class<?=($i % 2)+1?>"><?=($item['isAdmin'] ? 'x' : null)?></td>
		<td class="class<?=($i % 2)+1?>"><?=dh('m.d', $item['lastTime'])?></td>
		<td class="class<?=($i % 2)+1?>">
			<a href="/user/edit/<?=$item['id']?>">Edit</a><br />
			<a href="/user/erase/<?=$item['id']?>" onclick="return false;" class="actionConfirm">Delete</a><br />
			<a href="/user/duplicate/<?=$item['id']?>" onclick="duplicate.click('/user/duplicate/<?=$item['id']?>/');">Copy</a>
			<nobr><a href="/user/beta_grants/<?=$item['id']?>">Grants (Beta version)</a></nobr>
		</td>
	</tr>
	<?}?>
</table>
<?=$this->content('footer.php');?>