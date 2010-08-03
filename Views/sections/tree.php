
<div id="tree">
	<a href="/sections">Sections</a>
	<?if (isset($sectionTree) && $sectionTree && is_array($sectionTree)) {?>
		<?foreach ($sectionTree as &$item) {?>
			-> <a href="/section/details/<?=$item['id']?>"><?=$item['title']?></a>
		<?}?>
	<?}?>
</div>
