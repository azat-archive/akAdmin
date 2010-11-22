<?=$this->content('header.php');?>
<?=$this->content('sections/tree.php');?>
<?if (is_array($sections)) {?>
<table>
	<tr>
		<td width="1%">Ico</td>
		<td>Title</td>
		<td>Description</td>
		<td width="1%"><nobr>Sub sections</nobr></td>
		<td width="1%">Tables</td>
		<td width="1%">Actions</td>
	</tr>
	<?$i = 0; foreach ($sections as &$section) { $i++ ?>
	<tr>
		<td class="class<?=($i % 2)+1?>"><?=($section['tableName'] ? '<img src="/images/application-document.png" alt="Table" />' : '<img src="/images/blue-folder--arrow.png" alt="Section" />')?></td>
		<td class="class<?=($i % 2)+1?>"><a href="/section/details/<?=$section['id']?>"><?=$section['title']?></a></td>
		<td class="class<?=($i % 2)+1?>"><?=getTextLimited($section['description'])?></td>
		<td class="class<?=($i % 2)+1?>"><?=$section['subSections']?></td>
		<td class="class<?=($i % 2)+1?>"><?=$section['tables']?></td>
		<td class="class<?=($i % 2)+1?>">
			<nobr>
				<a href="/section/edit/<?=$section['id']?>"><img src="/images/edit.png" alt="Edit" title="Edit" /></a>
				<a href="/section/erase/<?=$section['id']?>" onclick="return false;" class="actionConfirm"><img src="/images/eraser.png" alt="Delete" title="Delete" /></a>
				<?if ($section['tableName']) {?>
				<a href="/section/settings/<?=$section['id']?>"><img src="/images/controller.png" alt="Settings" title="Settings" /></a>
				<?}?>
			</nobr>
		</td>
	</tr>
	<?}?>
</table>
<?} else {?>
	<h2 class="noAnything">You don`t have any sections</h2>
<?}?>
<?=$this->content('footer.php');?>