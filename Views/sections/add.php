<?=$this->content('header.php');?>
<form name="add" method="post">
	<ul class="form">
		<li><label><strong>Title</strong><input type="text" name="title" value="<?=gfs((isset($_POST['title']) ? $_POST['title'] : null))?>" /></label></li>
		<li><label><strong>Description</strong><textarea name="description"><?=gfs((isset($_POST['description']) ? $_POST['description'] : null))?></textarea></label></li>
		<li>
			<label><strong>Root section</strong><input type="text" name="parentTitle" value="<?=gfs((isset($_POST['parentTitle']) ? $_POST['parentTitle'] : null))?>" /></label>
			<input type="hidden" name="pid" value="<?=gfs((isset($_POST['pid']) ? $_POST['pid'] : null))?>" />
		</li>
		<li><label><strong>Table name</strong><input type="text" name="tableName" value="<?=gfs((isset($_POST['tableName']) ? $_POST['title'] : null))?>" /></label></li>
		
		<li><input type="submit" value="Submit" /></li>
	</ul>
</form>
<?=$this->content('footer.php');?>