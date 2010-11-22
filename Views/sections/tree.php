
<div id="tree">
	<img src="/images/folder-tree.png" alt="Tree" title="Tree" />
	<a href="/sections">Sections</a>
	<?if (isset($sectionTree) && $sectionTree && is_array($sectionTree)) {?>
		<?foreach ($sectionTree as &$item) {?>
			<a class="sub" href="/section/details/<?=$item['id']?>"><?=$item['title']?></a>
		<?}?>
	<?}?>
</div>
