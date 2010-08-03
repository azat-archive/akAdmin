<?=$this->content('header.php');?>
<?=$this->content('sections/tree.php');?>
<?if (is_array($fields)) {?>

<!-- select of avaliable actions for selected items -->
<div class="actionSelect"></div>
<!-- \select of avaliable actions for selected items -->

<form method="post">
	<table>
		<tr>
			<?if ($fieldAutoIncrement) {?>
			<td><a href="#" class="allSelectDeselect">Select</a></td>
			<?}?>
			
			<?foreach ($fields as $fieldName => &$fieldValue) {?>
			<td><?=sprintf(
				'<a%s href="/section/details/%u%s%s/sort/%s%s">%s</a>',
				(mb_substr(mb_strtolower($sort), 0, mb_strlen($fieldName)) == mb_strtolower($fieldName) ? ' class="selected"' : null),
				$currentSection['id'],
				($searchQuery ? sprintf('/search/%s', $searchQuery) : null),
				($page ? sprintf('/page/%u', $page) : null),
				$fieldName,
				((mb_substr(mb_strtolower($sort), 0, mb_strlen($fieldName)) == mb_strtolower($fieldName) && mb_substr($sort, -4) != 'desc') ? ' desc' : null),
				$fieldValue
			)?></td>
			<?}?>
			
			<?if ($fieldAutoIncrement) {?>
			<td>Actions</td>
			<?}?>
		</tr>
		<?if (is_array($items)) {?>
		<?$i = 0; foreach ($items as &$item) { $i++ ?>
		<tr>
			<?if ($fieldAutoIncrement) {?>
			<td class="class<?=($i % 2)+1?>">
				<input class="itemMultiActions" type="checkbox" name="id[]" value="<?=$item[$fieldAutoIncrement]?>" />
			</td>
			<?}?>
			
			<?foreach ($item as $key => &$value) {?>
			<?if ($key == $fieldAutoIncrement) {?>
			<td class="class<?=($i % 2)+1?>"><a href="/section/items/<?=$currentSection['id']?>/edit/<?=$value?>"><?=$value?></a></td>
			<?} else {?>
			<td class="class<?=($i % 2)+1?>"><?=getTextLimited($value)?></td>
			<?}?>
			<?}?>
			
			<?if ($fieldAutoIncrement) {?>
			<td class="class<?=($i % 2)+1?>">
				<a href="/section/items/<?=$currentSection['id']?>/erase/<?=$item[$fieldAutoIncrement]?>" onclick="return false;" class="actionConfirm">Delete</a><br />
				<a href="#" onclick="duplicate.click('/section/items/<?=$currentSection['id']?>/duplicate/<?=$item[$fieldAutoIncrement]?>/');">Copy</a><br />
			</td>
			<?}?>
		</tr>
		<?}?>
		<?} else {?>
			<tr><td colspan="20" align="middle"><h3>No items</h3></td></tr>
		<?}?>
	</table>
</form>

<!-- select of avaliable actions for selected items -->
<div class="actionSelect"></div>
<!-- \select of avaliable actions for selected items -->

<script type="text/javascript">
	// set ulr for multi delete and multi duplicate
	multiActions.urlErase = '/section/items/<?=$currentSection['id']?>/erase';
	multiActions.urlDuplicate = '/section/items/<?=$currentSection['id']?>/duplicate/';
</script>

<!-- paginator -->
<div class="paginator"></div> 
<script type="text/javascript">
	$('.paginator').paginator({
		pagesTotal:<?=$pages?>, 
		pagesSpan:10, 
		pageCurrent:<?=$page?>, 
		baseUrl: function (pageNum) {
			location.href = '/section/details/<?=$currentSection['id']?>/<?=($searchQuery ? sprintf('search/%s/', $searchQuery) : null)?>page/' + pageNum + '<?=($sort ? sprintf('/sort/%s', $sort) : null)?>';
		},
		lang: {
			next  : "Next",
			last  : "Last",
			prior : "Previous",
			first : "First",
			arrowRight : String.fromCharCode(8594),
			arrowLeft  : String.fromCharCode(8592)
		}
	});
</script>
<!-- \paginator -->

<?} else {?>
	<h2 class="noAnything">You don`t have any items</h2>
<?}?>
<?=$this->content('footer.php');?>