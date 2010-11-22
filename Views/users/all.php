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
			<nobr>
				<a href="/user/edit/<?=$item['id']?>"><img src="/images/edit.png" alt="Edit" title="Edit" /></a>
				<a href="/user/erase/<?=$item['id']?>" onclick="return false;" class="actionConfirm"><img src="/images/eraser.png" alt="Delete" title="Delete" /></a>
				<a href="/user/duplicate/<?=$item['id']?>/1" onclick="duplicate.click('/user/duplicate/<?=$item['id']?>/');"><img src="/images/document-copy.png" alt="Copy" title="Copy" /></a>
				<a href="/user/grants/<?=$item['id']?>"><img src="/images/user--plus.png" alt="Grants" title="Grants" /></a>
			</nobr>
		</td>
	</tr>
	<?}?>
</table>
<?=$this->content('footer.php');?>